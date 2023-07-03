<?php

namespace PrestaShop\Module\PayEye\Module;

class HookModuleRoutes
{
    /** @var \PayEye */
    private $module;

    public function __construct(\PayEye $module)
    {
        $this->module = $module;
    }

    public function __invoke(): array
    {
        return [
            'module-payeye-cart' => $this->registerRouter('Cart', 'carts'),
            'module-payeye-cart-promo-codes' => $this->registerRouter('PromoCode', 'carts/promo-codes'),
            'module-payeye-orders' => $this->registerRouter('Order', 'orders'),
            'module-payeye-orders-update' => $this->registerRouter('OrderUpdate', 'orders/status'),
            'module-payeye-widget' => $this->registerRouter('Widget', 'widget'),
            'module-payeye-widget-status' => $this->registerRouter('WidgetStatus', 'widget/status'),
            'module-payeye-returns' => $this->registerRouter('Return', 'returns'),
            'module-payeye-returns-status' => $this->registerRouter('ReturnStatus', 'returns/status'),
        ];
    }

    private function registerRouter(string $controller, string $path): array
    {
        return [
            'controller' => $controller,
            'rule' => \PayEye::NAMESPACE . '/' . $path,
            'keywords' => [],
            'params' => [
                'fc' => 'module',
                'module' => $this->module->name,
            ],
        ];
    }
}
