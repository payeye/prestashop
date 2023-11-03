<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Widget;

class WidgetUI
{
    /** @var int */
    private $bottom;

    /** @var bool */
    private $mobileOpen;

    /** @var bool */
    private $widgetVisible;

    public function __construct(int $bottom, bool $mobileOpen, bool $widgetVisible)
    {
        $this->bottom = $bottom;
        $this->mobileOpen = $mobileOpen;
        $this->widgetVisible = $widgetVisible;
    }

    public function getBottom(): int
    {
        return $this->bottom;
    }

    public function getMobileOpen(): bool
    {
        return $this->mobileOpen;
    }

    public function getWidgetVisible(): bool
    {
        return $this->widgetVisible;
    }
}

