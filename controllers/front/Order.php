<?php

declare(strict_types=1);

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Exception\CartContentNotMatchedException;
use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Lib\Exception\OrderAlreadyExistsException;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\Order\OrderRequestModel;
use PayEye\Lib\Order\OrderResponseModel;
use PayEye\Lib\Service\AmountService;
use PrestaShop\Module\PayEye\Cart\Services\CartHashService;
use PrestaShop\Module\PayEye\Cart\Services\CartResponseService;
use PrestaShop\Module\PayEye\Cart\Services\ShippingService;
use PrestaShop\Module\PayEye\Controller\FrontController;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

defined('_PS_VERSION_') || exit;

class PayEyeOrderModuleFrontController extends FrontController
{
    /** @var PayEye */
    public $module;

    /** @var Order */
    private $order;

    public function postProcess(): void
    {
        try {
            $amountService = new AmountService();

            $handleRequest = $this->getRequest();
            $this->checkPermission($handleRequest);

            $request = new OrderRequestModel($handleRequest);

            $cartMapping = PayEyeCartMapping::findByCartUuid($request->getCartId());
            if ($cartMapping === null) {
                throw new CartNotFoundException();
            }

            $cart = new Cart($cartMapping->id_cart);

            $this->context->customer = new Customer($cart->id_customer);
            $this->context->cart = $cart;

            if ($request->getCartHash() !== $this->calculateCartHash($this->currentCart($amountService))) {
                throw new CartContentNotMatchedException();
            }

            $this->exitWithResponse($this->createOrder($amountService, $request)->toArray());
        } catch (PayEyePaymentException $exception) {
            $this->exitWithPayEyeExceptionResponse($exception);
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws Exception
     */
    private function createOrder(AmountService $amountService,OrderRequestModel $requestModel): OrderResponseModel
    {
        $currency = $this->context->currency;
        $total = (float) $this->context->cart->getOrderTotal(true, Cart::BOTH);

        $this->context->cart->secure_key = $this->context->customer->secure_key;
        $this->context->cart->save();

        if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->orderExists()) {
            throw new OrderAlreadyExistsException();
        }

        $this->module->validateOrder(
            $this->context->cart->id,
            $this->module->orderStatuses->getCreated(),
            $total,
            $this->module->displayName,
            null,
            [],
            (int) $currency->id,
            false,
            $this->context->customer->secure_key
        );

        $this->order = new Order($this->module->currentOrder);

        $order = $this->order;

        if ($requestModel->getShipping()->getPickupPoint()) {
            $order->note = $requestModel->getShipping()->getPickupPoint()->getName() .', ' . $requestModel->getShipping()->getAddress()->getFullAddress();
            $order->save();
        }

        return OrderResponseModel::builder()
            ->setCheckoutUrl($this->checkoutUrl())
            ->setOrderId((string) $order->id)
            ->setTotalAmount($amountService->convertFloatToInteger($order->total_paid))
            ->setCartAmount($amountService->convertFloatToInteger($order->total_products_wt))
            ->setShippingAmount($amountService->convertFloatToInteger($order->total_shipping))
            ->setCurrency(Currency::getIsoCodeById((int) $order->id_currency));
    }

    private function checkoutUrl(): string
    {
        $order = $this->order;
        $customer = $this->context->customer;

        return $this->context->link->getPageLink('guest-tracking') . "?controller=guest-tracking&order_reference=$order->reference&email=$customer->email";
    }

    private function currentCart(AmountService $amountService): CartResponseModel
    {
        $checkoutSessionCore = new CheckoutSessionCore(
            $this->context, new DeliveryOptionsFinder(
                $this->context,
                $this->context->getTranslator(),
                new ObjectPresenter(),
                new PriceFormatter()
            )
        );

        $cart = $this->context->cart;
        $shippingService = new ShippingService($checkoutSessionCore->getDeliveryOptions(), $amountService, $this->module);
        $cartResponseService = new CartResponseService($cart, $amountService);

        return CartResponseModel::builder()
            ->setPromoCodes($cartResponseService->promoCodes)
            ->setShippingMethods($shippingService->shippingMethods)
            ->setCart($cartResponseService->payeyeCart)
            ->setShippingId((string) $cart->id_carrier)
            ->setCurrency(Currency::getIsoCodeById((int) $cart->id_currency))
            ->setProducts($cartResponseService->products);
    }

    private function calculateCartHash(CartResponseModel $cart): string
    {
        return (new CartHashService($this->module->authConfig))->calculateCartHash($cart);
    }
}
