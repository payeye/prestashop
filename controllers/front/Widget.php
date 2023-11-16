<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

use chillerlan\QRCode\QRCode;
use PayEye\Lib\Deeplink\Deeplink;
use PayEye\Lib\Widget\Model\WidgetCartModel;
use PayEye\Lib\Widget\WidgetModel;
use PrestaShop\Module\PayEye\Controller\FrontController;

class PayEyeWidgetModuleFrontController extends FrontController
{
    public function postProcess()
    {
        $response = null;
        $cartId = $this->context->cart->id;

        if ($cartId === null) {
            $this->exitWithResponse($response);
        }

        $entity = PayEyeCartMapping::findByCartId($cartId);

        if ($entity === null || $this->context->cart->hasProducts() === false) {
            $this->exitWithResponse($response);
        }

        $price = $this->context->cart->getSummaryDetails()['total_price'];
        $shippingPrice = $this->context->cart->getSummaryDetails()['total_shipping'];
        $price= $price - $shippingPrice;
        $price = number_format((float) $price, 2, ',', ' ');

        $qrCode = new QRCode();
        $deepLink = Deeplink::create($this->module->config, $this->module->authConfig, $entity->uuid);

        $widget = WidgetModel::builder()
            ->setDeepLink($deepLink)
            ->setCart(
                WidgetCartModel::builder()
                    ->setId($entity->uuid)
                    ->setOpen($entity->open)
                    ->setPrice($price)
                    ->setRegularPrice($price)
                    ->setQr($qrCode->render($deepLink))
                    ->setCount($this->context->cart->nbProducts())
            );

        $this->exitWithResponse($widget->toArray());
    }
}
