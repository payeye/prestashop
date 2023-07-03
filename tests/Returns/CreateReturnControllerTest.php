<?php

namespace PayEye\Tests\Returns;

use PayEye\Lib\Cart\CartResponseModel;
use PayEye\Lib\Enum\OrderStatus;
use PayEye\Lib\Model\RefundProduct;
use PayEye\Lib\Order\OrderCreateResponseModel;
use PayEye\Lib\Order\OrderUpdateStatusRequestModel;
use PayEye\Lib\Returns\ReturnCreateRequestModel;
use PayEye\Lib\Returns\ReturnCreateResponseModel;
use PayEye\Tests\Shared\BaseTestCase;

class CreateReturnControllerTest extends BaseTestCase
{
    public function testCreateReturn(): void
    {
        $this->getCart($this->mock);
        $cart = CartResponseModel::createFromArray($this->response->getArrayResponse());

        $mock = $this->mock;
        $mock['shippingId'] = $cart->shippingId;
        $mock['cartHash'] = $cart->cartHash;
        $this->createOrder($mock);

        $createdOrder = OrderCreateResponseModel::createFromArray($this->response->getArrayResponse());

        $request = OrderUpdateStatusRequestModel::builder()
            ->setOrderId($createdOrder->orderId)
            ->setStatus(OrderStatus::SUCCESS);

        $this->updateOrderStatus($request->toArray());

        $cartProduct = $cart->products[0];

        $returnProduct = RefundProduct::builder()
            ->setId($cartProduct->id)
            ->setQuantity(1)
            ->setVariantId($cartProduct->variantId);

        $requestModel = ReturnCreateRequestModel::builder()
            ->setOrderId((int) $createdOrder->orderId)
            ->setCurrency('PLN')
            ->setProducts([$returnProduct]);

        $this->createReturn($requestModel);
        $createdReturn = ReturnCreateResponseModel::createFromArray($this->response->getArrayResponse());

        $order = new \Order((int) $createdOrder->orderId);

        $this->assertSame((int) $order->current_state, $this->module->orderStatuses->getReturnRequest());

        $returnEntity = new \PayEyeOrderReturn($createdReturn->returnId);
        $this->assertNotNull($returnEntity->id, 'Return entity not exists');
        $this->assertNotEmpty($returnEntity->getProducts(), 'Return entity has not products');
    }
}
