<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

use PayEye\Lib\Enum\OrderStatus;
use PayEye\Lib\Widget\WidgetStatusModel;
use PrestaShop\Module\PayEye\Controller\FrontController;

class PayEyeWidgetStatusModuleFrontController extends FrontController
{
    public function postProcess()
    {
        $request = Tools::getAllValues();
        $cartId = $request['cartId'];

        $cart = PayEyeCartMapping::findByCartUuid($cartId);

        if ($cart === null) {
            $this->exitWithResponse(null);
        }

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->exitWithResponse($this->widgetStatus($cart));
                break;
            case 'PUT':
                $this->resetWidget($cart);
                $this->exitWithResponse(null);
        }
    }

    private function widgetStatus(PayEyeCartMapping $entity): array
    {
        $response = WidgetStatusModel::builder();

        if ($entity->open) {
            $response->setOpen(true);
        }

        $order = Order::getByCartId($entity->id_cart);

        if ($order === null) {
            return $response->toArray();
        }

        $response->setStatus('ORDER_CREATED');

        switch ($order->current_state) {
            case $this->module->orderStatuses->getSuccess():
                $response->setStatus(OrderStatus::SUCCESS);
                break;
            case $this->module->orderStatuses->getRejected():
                $response->setStatus(OrderStatus::REJECTED);
                break;
        }

        // @TODO extend model with checkoutUrl
        // @TODO use model in WooCommerce

        $customer = new Customer($order->id_customer);

        $data = $response->toArray();
        $data['checkoutUrl'] = $this->context->link->getPageLink('guest-tracking') . "?controller=guest-tracking&order_reference=$order->reference&email=$customer->email";

        return $data;
    }

    private function resetWidget(PayEyeCartMapping $entity): void
    {
        $entity->open = false;
        $entity->update();
    }
}
