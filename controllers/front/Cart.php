<?php

use PrestaShop\Module\PayEye\Api;

defined('_PS_VERSION_') || exit;

class PayEyeCartModuleFrontController extends ModuleFrontController
{
    use Api;

    /** @var PayEye */
    public $module;

    public function postProcess(): void
    {
        $request = $this->getRequest();

        Context::getContext()->cart = new Cart($request['id']);

        $cart = $this->context->cart;
        $cart_products = $cart->getProducts();
        foreach ($cart_products as $product) {
            $product_price[] = $product;
        }

        header('Content-Type: application/json;');
        echo json_encode($cart);
        exit;
    }
}
