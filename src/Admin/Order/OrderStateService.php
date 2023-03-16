<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Order;

class OrderStateService
{
    /** @var \PayEye */
    private $module;

    public function __construct(\PayEye $module)
    {
        $this->module = $module;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function addOrderState(string $state, array $names): int
    {
        $stateId = (int) \Configuration::getGlobalValue($state);

        if ($stateId) {
            return $stateId;
        }

        return $this->createOrderState($names);
    }

    /**
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function createOrderState(array $names): int
    {
        $order_state = new \OrderState();

        foreach ($names as $code => $name) {
            $order_state->name[\Language::getIdByIso($code)] = $name;
        }

        $order_state->send_email = false;
        $order_state->invoice = false;
        $order_state->unremovable = true;
        $order_state->color = '#34209E';
        $order_state->template = 'payment';
        $order_state->module_name = $this->module->name;

        $order_state->add();

        return (int) $order_state->id;
    }

    private function stateAlreadyExists(int $orderStateId): bool
    {
        $query = new \DbQuery();
        $query->select('id_order_state');
        $query->from('order_state');
        $query->where('id_order_state = ' . $orderStateId);
        $query->where('module_name = "' . $this->module->name . '"');

        return (bool) \Db::getInstance()->getValue($query);
    }
}
