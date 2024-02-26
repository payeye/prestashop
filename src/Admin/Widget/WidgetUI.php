<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Widget;

use PayEye\Lib\Enum\WidgetButtonStyles;
use PayEye\Lib\Enum\WidgetModes;

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
    private $widgetMode = '';
    private $onClickButtonStyle = '';
    private $allowedWidgetModes = [
        WidgetModes::FLOATING,
        WidgetModes::ON_CLICK
    ];
    private $allowedOnClickButtonStyles = [
        WidgetButtonStyles::STYLED_GREEN,
        WidgetButtonStyles::STYLED_WHITE,
        WidgetButtonStyles::CUSTOM
    ];

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
    /**
     * Set the widget mode.
     *
     * @param string $widgetMode
     * @return self
     */
    public function setWidgetMode(string $widgetMode): self
    {
        if (!in_array($widgetMode, $this->allowedWidgetModes)) {
            $this->widgetMode = $this->allowedWidgetModes[0];
            return $this;
        }
        $this->widgetMode = $widgetMode;
        return $this;
    }

    /**
     * Get the widget mode.
     *
     * @return string
     */
    public function getWidgetMode(): string
    {
        if (!in_array($this->widgetMode, $this->allowedWidgetModes)) {
            return $this->allowedWidgetModes[0];
        }
        return $this->widgetMode;
    }

    /**
     * Set the on-click button style.
     *
     * @param string $onClickButtonStyle
     * @return self
     */
    public function setOnClickButtonStyle(string $onClickButtonStyle): self
    {
        if (!in_array($onClickButtonStyle, $this->allowedOnClickButtonStyles)) {
            $this->onClickButtonStyle = $this->allowedOnClickButtonStyles[0];
            return $this;
        }
        $this->onClickButtonStyle = $onClickButtonStyle;
        return $this;
    }

    /**
     * Get the on-click button style.
     *
     * @return string
     */
    public function getOnClickButtonStyle(): string
    {
        if (!in_array($this->onClickButtonStyle , $this->allowedOnClickButtonStyles)) {
            return $this->allowedOnClickButtonStyles[0];
        }
        return $this->onClickButtonStyle;
    }
}
