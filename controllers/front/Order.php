<?php

declare(strict_types=1);

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Exception\CartContentNotMatchedException;
use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Lib\Exception\OrderAlreadyExistsException;
use PayEye\Lib\Exception\OrderPriceNotMatchedException;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\Order\OrderCreateRequestModel;
use PayEye\Lib\Order\OrderCreateResponseModel;
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

            $request = new OrderCreateRequestModel($handleRequest);

            $cartMapping = PayEyeCartMapping::findByCartUuid($request->getCartId());
            if ($cartMapping === null) {
                throw new CartNotFoundException();
            }

            $cart = new Cart($cartMapping->id_cart);

            $this->context->customer = new Customer($cart->id_customer);
            $this->context->cart = $cart;

            $cartService = $this->currentCart($amountService);
            if ($request->getCartHash() !== $this->calculateCartHash($cartService)) {
                throw new CartContentNotMatchedException();
            }

            $address = new Address($this->context->cart->id_address_delivery);
            $address->address1 = $request->getShipping()->getAddress()->getFirstLine();
            $address->city = $request->getShipping()->getAddress()->getCity();
            $address->postcode = $request->getShipping()->getAddress()->getPostCode();

            if ($request->getShipping()->getPickupPoint()) {
                $address->address1 = $request->getShipping()->getPickupPoint()->getName();
                $address->address2 = $request->getShipping()->getAddress()->getFirstLine();
            }
            $address->save();

            \Hook::exec('actionPayEyeApiBeforeCreateOrder', [
                'shippingProvider' => $request->getShippingProvider(),
                'pickupPointName' => $this->getPickupPointName($request),
            ]);

            $order = $this->createOrder($amountService, $request);

            if ($cartService->cart->total !== $order->totalAmount) {
                throw new OrderPriceNotMatchedException();
            }

            $this->exitWithResponse($order->toArray());
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
    private function createOrder(AmountService $amountService, OrderCreateRequestModel $requestModel): OrderCreateResponseModel
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

        return OrderCreateResponseModel::builder()
            ->setCheckoutUrl($this->checkoutUrl())
            ->setOrderId((string) $order->id)
            ->setTotalAmount($amountService->convertFloatToInteger($order->getOrdersTotalPaid()))
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

    private function getPickupPointName(OrderCreateRequestModel $request): ?string
    {
        $shipping = $request->getShipping();

        $pickupPoint = $shipping->getPickupPoint();

        if ($pickupPoint === null) {
            return null;
        }

        return $pickupPoint->getName();
    }
}
