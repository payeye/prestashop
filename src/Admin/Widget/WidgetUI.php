<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Widget;

class WidgetUI
{
    /** @var int */
    private $bottom;

    public function __construct(int $bottom)
    {
        $this->bottom = $bottom;
    }

    public function getBottom(): int
    {
        return $this->bottom;
    }
}
