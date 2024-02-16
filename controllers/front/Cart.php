<?php

declare(strict_types=1);

use PayEye\Lib\Cart\CartRequestModel;
use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\CartType;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\Model\Shipping;
use PayEye\Lib\Model\Shop as PayEyeShop;
use PayEye\Lib\Service\AddressService;
use PayEye\Lib\Service\AmountService;
use PayEye\Lib\Tool\Uuid;
use PrestaShop\Module\PayEye\Cart\Services\CartHashService;
use PrestaShop\Module\PayEye\Cart\Services\CartResponseService;
use PrestaShop\Module\PayEye\Cart\Services\CartService;
use PrestaShop\Module\PayEye\Cart\Services\ShippingService;
use PrestaShop\Module\PayEye\Controller\FrontController;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

defined('_PS_VERSION_') || exit;

class PayEyeCartModuleFrontController extends FrontController
{
    /** @var PayEye */
    public $module;

    public function postProcess(): void
    {
        try {
            $handleRequest = $this->getRequest();
            $this->checkPermission($handleRequest);

            $request = CartRequestModel::createFromArray($handleRequest);

            $entityCartMapping = PayEyeCartMapping::findByCartUuid($request->getCartId());

            if ($entityCartMapping === null) {
                throw new CartNotFoundException();
            }

            if ($entityCartMapping->open === false) {
                $entityCartMapping->open = true;
                $entityCartMapping->update();
            }

            $this->context->cart = new Cart($entityCartMapping->id_cart);

            $response = $this->cartResponse($request);

            $this->exitWithResponse($response->toArray());
        } catch (PayEyePaymentException $exception) {
            $this->exitWithPayEyeExceptionResponse($exception);
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    private function cartResponse(CartRequestModel $request): CartResponseModel
    {
        $amountService = new AmountService();

        $customer = $this->createCustomer($request);

        $this->context->customer = $customer;

        $deliveryAddress = $this->createDeliveryAddress($request);
        $invoiceAddress = $this->createInvoiceAddress($request);

        $this->context->cart->id_customer = $customer->id;
        $this->context->cart->id_address_delivery = $deliveryAddress->id;
        $this->context->cart->id_address_invoice = $invoiceAddress->id;

        if (version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
            $objectPresenterClass = '\PrestaShop\PrestaShop\Adapter\ObjectPresenter';
            $objectPresenter = new $objectPresenterClass();
        } else {
            $objectPresenterClass = '\PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter';
            $objectPresenter = new $objectPresenterClass();
        }

        $checkoutSessionCore = new CheckoutSessionCore(
            $this->context,
            new DeliveryOptionsFinder(
                $this->context,
                $this->context->getTranslator(),
                $objectPresenter,
                new PriceFormatter()
            )
        );

        $checkoutSessionCore->setIdAddressDelivery($deliveryAddress->id);

        $cartService = new CartService($this->context->cart);
        $shippingService = new ShippingService($checkoutSessionCore->getDeliveryOptions(), $amountService, $this->module, $cartService);
        $shippingId = $this->getShippingId($shippingService, $request);
        $this->context->cart->setDeliveryOption([
            $deliveryAddress->id => $shippingId . ',',
        ]);

        $cartResponseService = new CartResponseService($this->context->cart, $amountService);

        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cart->save();

        $currencyId = (int)$this->context->cart->id_currency;
        $currency = new Currency($currencyId);

        $cartResponse = CartResponseModel::builder()
            ->setShop($this->getShop())
            ->setPromoCodes($cartResponseService->promoCodes)
            ->setShippingId($shippingId)
            ->setShippingMethods($shippingService->getShippingMethods())
            ->setCart($cartResponseService->payeyeCart)
            ->setCurrency($currency->iso_code)
            ->setProducts($cartResponseService->products)
            ->setSignatureFrom(SignatureFrom::GET_CART_RESPONSE)
            ->setCartType($cartService->getCartType());

        $cartResponse->setCartHash($this->calculateCartHash($cartResponse));

        return $cartResponse;
    }

    /**
     * @throws PrestaShopException
     * @throws Exception
     */
    private function createCustomer(CartRequestModel $request): Customer
    {
        $customer = new Customer($this->context->cart->id_customer);
        $billing = $request->getBilling();

        if ($billing) {
            $customer->firstname = $billing->getFirstName();
            $customer->lastname = $billing->getLastName();
            $customer->email = $billing->getEmail();
        } else {
            $customer->firstname = 'null';
            $customer->lastname = 'null';
            $customer->email = 'customer@payeye.com';
        }

        $customer->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-' . Configuration::get('PS_PASSWD_TIME_FRONT') . 'minutes'));
        $customer->secure_key = md5(uniqid((string) random_int(0, mt_getrandmax()), true));
        $customer->setWsPasswd(Uuid::generate());
        $customer->is_guest = true;
        $customer->save();

        return $customer;
    }

    /**
     * @throws PrestaShopException
     */
    private function createDeliveryAddress(CartRequestModel $request): Address
    {
        $shipping = $request->getShipping();

        $address = new Address($this->context->cart->id_address_delivery);
        $address->id_customer = $this->context->customer->id;
        $shop_country_name = Configuration::get('PS_SHOP_COUNTRY');
        $countries = Country::getCountries($active = true); // Pobranie wszystkich krajów
        foreach ($countries as $country) {
            if ($country['name'] === $shop_country_name) {
                $countryISO = $country['iso_code'];
                break;
            }
        }
        if (!empty($countryISO)) {
            $country = Country::getByIso($countryISO);
        } else {
            $country = 0;
        }

        if ($shipping) {
            $addressService = AddressService::create($shipping->getAddress());
            $addressService->build();
            $address->id_country = Country::getByIso($shipping->getAddress()->getCountry());
            $address->alias = $shipping->getLabel();
            $address->firstname = $shipping->getFirstName();
            $address->lastname = $shipping->getLastName();
            $address->address1 = $addressService->getFirstLine();
            $address->city = $shipping->getAddress()->getCity();
            $address->postcode = $shipping->getAddress()->getPostCode();

            if ($request->getBilling()) {
                $address->phone = $request->getBilling()->getPhoneNumber();
                $address->phone_mobile = $request->getBilling()->getPhoneNumber();
            }
        } else {
            $address->id_country = $country;
            $address->alias = ' ';
            $address->firstname = ' ';
            $address->lastname = ' ';
            $address->address1 = ' ';
            $address->city = ' ';
            $address->postcode = ' ';
            $address->phone = ' ';
            $address->phone_mobile = ' ';
        }

        $address->save();

        return $address;
    }

    /**
     * @throws PrestaShopException
     */
    private function createInvoiceAddress(CartRequestModel $request): Address
    {
        $address = new Address($this->context->cart->id_address_delivery === $this->context->cart->id_address_invoice ? null : $this->context->cart->id_address_invoice);
        $billing = $request->getBilling();

        $address->id_customer = $this->context->customer->id;

        $shop_country_name = Configuration::get('PS_SHOP_COUNTRY');
        $countries = Country::getCountries($active = true); // Pobranie wszystkich krajów

        foreach ($countries as $country) {
            if ($country['name'] === $shop_country_name) {
                $countryISO = $country['iso_code'];
                break;
            }
        }
        if (!empty($countryISO)) {
            $country = Country::getByIso($countryISO);
        } else {
            $country = 0;
        }


        if ($billing) {
            $addressService = AddressService::create($billing->getAddress());
            $addressService->build();
            $address->id_country = Country::getByIso($billing->getAddress()->getCountry());
            $address->alias = ' ';
            // $address->dni = ' ';
            // $address->vat_number = ' ';
            $address->firstname = $billing->getFirstName();
            $address->lastname = $billing->getLastName();
            $address->address1 = $addressService->getFirstLine();
            $address->city = $billing->getAddress()->getCity();
            $address->postcode = $billing->getAddress()->getPostCode();
        } else {
            $address->id_country = $country;
            $address->alias = ' ';
            $address->firstname = ' ';
            $address->lastname = ' ';
            $address->address1 = ' ';
            $address->city = ' ';
            $address->postcode = ' ';
        }

        $address->save();

        return $address;
    }

    private function getShop(): PayEyeShop
    {
        return PayEyeShop::builder()
            ->setName(Configuration::get('PS_SHOP_NAME'))
            ->setUrl($this->context->shop->getBaseURL());
    }

    private function calculateCartHash(CartResponseModel $cart): string
    {
        return (new CartHashService($this->module->authConfig))->calculateCartHash($cart);
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
            return $shippingService->getDefaultShipping($cartRequestModel->getShippingProvider())->id ?? null;
        }

        return $cartRequestModel->getShippingId();
    }
}
