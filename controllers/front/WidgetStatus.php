<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

use PayEye\Lib\Enum\OrderStatus;
use PayEye\Lib\Widget\WidgetStatusModel;
use PrestaShop\Module\PayEye\Controller\FrontController;
use PrestaShop\Module\PayEye\Entity\PayEyeCartMappingEntity;

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
                $this->exitWithResponse($this->widgetStatus($cart)->toArray());
                break;
            case 'PUT':
                $this->resetWidget($cart);
                $this->exitWithResponse(null);
        }
    }

    private function widgetStatus(PayEyeCartMappingEntity $entity): WidgetStatusModel
    {
        $response = WidgetStatusModel::builder();

        if ($entity->open) {
            $response->setOpen(true);
        }

        $order = Order::getByCartId($entity->id_cart);

        if ($order === null) {
            return $response;
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

        return $response;
    }

    private function resetWidget(PayEyeCartMappingEntity $entity): void
    {
        $cartMapping = new PayEyeCartMapping();
        $cartMapping->setEntity($entity->setOpen(false));
        $cartMapping->update();
    }
}
