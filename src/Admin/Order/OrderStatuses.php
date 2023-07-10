<?php

declare(strict_types=1);

namespace PrestaShop\Module\PayEye\Admin\Order;

class OrderStatuses
{
    /** @var int */
    private $created;

    /** @var int */
    private $success;

    /** @var int */
    private $rejected;

    /** @var int */
    private $returnRequest;

    public function __construct(int $orderCreated, int $orderSuccess, int $orderRejected, int $returnRequest)
    {
        $this->created = $orderCreated;
        $this->success = $orderSuccess;
        $this->rejected = $orderRejected;
        $this->returnRequest = $returnRequest;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getRejected(): int
    {
        return $this->rejected;
    }

    public function getReturnRequest(): int
    {
        return $this->returnRequest;
    }
}
