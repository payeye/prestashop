<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Widget;

class WidgetUI
{
    /** @var int */
    private $bottom = 20;
    private $sidePosition = 20;
    private $zIndex = 20;
    private $side = 'right';

    /** @var bool */
    private $mobileOpen;

    /** @var bool */
    private $widgetVisible;

    public function __construct()
    {
        return $this;
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

    public function getSidePosition(): int
    {
        return $this->sidePosition;
    }

    public function getZIndex(): int
    {
        return $this->zIndex;
    }

    public function getSide(): string
    {
        return $this->side;
    }

    public function setBottom(int $bottom): self
    {
        $this->bottom = $bottom;

        return $this;
    }

    public function setSidePosition(int $sidePosition): self
    {
        $this->sidePosition = $sidePosition;

        return $this;
    }

    public function setZIndex(int $zIndex): self
    {
        $this->zIndex = $zIndex;

        return $this;
    }

    public function setSide($side): self
    {
        $allowedValues = ['LEFT', 'RIGHT'];
        $side = (in_array($side, $allowedValues)) ? $side : reset($allowedValues);
        $this->side = $side;

        return $this;
    }

    public function setMobileOpen(bool $mobileOpen): self
    {
        $this->mobileOpen = $mobileOpen;

        return $this;
    }

    public function setWidgetVisible(bool $widgetVisible): self
    {
        $this->widgetVisible = $widgetVisible;

        return $this;
    }
}
