<?php

use PrestaShop\Module\PayEye\Api;

class PayEyeCartModuleFrontController extends ModuleFrontController
{
    use Api;

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
