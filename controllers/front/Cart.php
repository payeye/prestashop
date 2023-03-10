<?php

declare(strict_types=1);

use PayEye\Lib\Auth\HashService;
use PayEye\Lib\Cart\CartRequestModel;
use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\Model\Cart as PayEyeCart;
use PayEye\Lib\Model\Shop as PayEyeShop;
use PayEye\Lib\Service\AmountService;
use PayEye\Lib\Tool\Uuid;
use PrestaShop\Module\PayEye\Controller\FrontController;
use PrestaShop\Module\PayEye\Service\CartService;
use PrestaShop\Module\PayEye\Service\ShippingService;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

defined('_PS_VERSION_') || exit;

class PayEyeCartModuleFrontController extends FrontController
{
    /** @var PayEye */
    public $module;

    public function postProcess(): void
    {
        try {
            $amountService = new AmountService();
            $hashService = new HashService($this->module->authConfig);

            $handleRequest = $this->getRequest();
            $this->checkPermission($handleRequest);

            $request = new CartRequestModel($handleRequest);
            $cartMapping = PayEyeCartMapping::findByCartUuid($request->getCartId());

            if ($cartMapping === null) {
                throw new CartNotFoundException();
            }

            $this->context->cart = new Cart($cartMapping->id_cart);

            $customer = new Customer($this->context->cart->id_customer);
            $customer->firstname = 'Dupa';
            $customer->lastname = 'Bury';
            $customer->email = 'bartosz.bury@payeye.com';
            $customer->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));
            $customer->secure_key = md5(uniqid((string)random_int(0, mt_getrandmax()), true));
            $customer->setWsPasswd(Uuid::generate());
            $customer->is_guest = true;
            $customer->save();

            $this->context->customer = $customer;

            $address = new Address($this->context->cart->id_address_delivery);
            $address->id_country = Country::getByIso($request->getShipping()->getAddress()->getCountry());
            $address->alias = 'PayEye Address';
            $address->lastname = 'Bury';
            $address->firstname = 'Bartosz';
            $address->address1 = 'aleja Wiśniowa 85B/(P)';
            $address->city = 'Wrocław';
            $address->postcode = $request->getShipping()->getAddress()->getPostCode();
            $address->save();

            $this->context->cart->id_customer = $customer->id;
            $this->context->cart->id_address_delivery = $address->id;
            $this->context->cart->id_address_invoice = $address->id;

            $checkoutSessionCore = new CheckoutSessionCore(
                $this->context, new DeliveryOptionsFinder(
                    $this->context,
                    $this->context->getTranslator(),
                    new ObjectPresenter(),
                    new PriceFormatter()
                )
            );

            $checkoutSessionCore->setIdAddressDelivery($address->id);

            $shippingService = new ShippingService($checkoutSessionCore->getDeliveryOptions(), $amountService, $this->module);
            $shippingId = $this->getShippingId($shippingService, $request);

            $this->context->cart->setDeliveryOption([
                $address->id => $shippingId.",",
            ]);

            $cartService = new CartService($this->context->cart, $amountService);

            $this->context->cart->secure_key = $customer->secure_key;
            $this->context->cart->save();

            $cartResponse = CartResponseModel::builder()
                ->setShop($this->getShop())
                ->setPromoCodes([])
                ->setShippingId($shippingId)
                ->setShippingMethods($shippingService->getShippingMethods())
                ->setCart($this->getCart($cartService))
                ->setCurrency(Currency::getIsoCodeById((int)$this->context->cart->id_currency))
                ->setProducts($cartService->getProducts())
                ->setSignatureFrom(SignatureFrom::GET_CART_RESPONSE);

            $cartResponse->setCartHash($this->calculateCartHash($cartResponse, $hashService));

            $this->exitWithResponse($cartResponse->toArray());
        } catch (PayEyePaymentException $exception) {
            $this->exitWithPayEyeExceptionResponse($exception);
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }

    private function getShop(): PayEyeShop
    {
        return PayEyeShop::builder()
            ->setName(Configuration::get('PS_SHOP_NAME'))
            ->setUrl($this->context->shop->getBaseURL());
    }

    private function getCart(CartService $cartService): PayEyeCart
    {
        $total = $cartService->getTotalAmount();
        $regularTotal = $cartService->getRegularProductsTotal() + $cartService->getShippingAmount();

        return PayEyeCart::builder()
            ->setTotal($total)
            ->setRegularTotal($regularTotal)
            ->setDiscount(0)
            ->setProducts($cartService->getProductsTotal())
            ->setRegularProducts($cartService->getRegularProductsTotal());
    }

    private function calculateCartHash(CartResponseModel $cart, HashService $hashService): string
    {
        return $hashService->cartHash(
            $cart->promoCodes,
            $cart->shippingMethods,
            $cart->cart,
            $cart->shippingId,
            $cart->currency,
            $cart->products
        );
    }

    private function getShippingId(ShippingService $shippingService, CartRequestModel $cartRequestModel): ?string
    {
        if (empty($shippingService->getShippingMethods())) {
            return null;
        }

        $shipping = array_filter($shippingService->getShippingMethods(), static function ($shipping) use ($cartRequestModel) {
            return $shipping->id === $cartRequestModel->getShippingId();
        });

        if (empty($shipping)) {
            return $shippingService->getDefaultShipping($cartRequestModel->getDeliveryType())->id ?? null;
        }

        return $cartRequestModel->getShippingId();
    }
}
