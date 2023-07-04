<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Widget;

class WidgetUI
{
    /** @var int */
    private $bottom;

    /** @var bool */
    private $mobileOpen;

    public function __construct(int $bottom, bool $mobileOpen)
    {
        $this->bottom = $bottom;
        $this->mobileOpen = $mobileOpen;
    }

    public function getBottom(): int
    {
        return $this->bottom;
    }

    public function getMobileOpen(): bool
    {
        return $this->mobileOpen;
    }
}
