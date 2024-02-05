<?php

use PayEye\Lib\Auth\AuthService;
use PayEye\Lib\Auth\HashService;
use PayEye\Lib\Enum\ReturnStatus;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Enum\TransferStatus;
use PayEye\Lib\HttpClient\Exception\HttpException;
use PayEye\Lib\HttpClient\Exception\HttpNetworkException;
use PayEye\Lib\HttpClient\Model\ReturnStatusRequest;
use PayEye\Lib\HttpClient\PayEyeHttpClient;
use PayEye\Lib\Service\AmountService;
use PayEye\Lib\Tool\JsonHelper;
use PrestaShop\Module\PayEye\Controller\AdminController;

class AdminAjaxReturnController extends AdminController
{
    public function ajaxProcessGetReturns(): void
    {
        $orderId = Tools::getValue('orderId');
        $returns = PayEyeOrderReturn::findByOrderId($orderId);

        $context = [];
        foreach ($returns as $return) {
            $products = $return->getProducts();
            $dataProducts = [];

            foreach ($products as $returnProduct) {
                $product = new ProductCore($returnProduct->id_product);
                $productAttributes = $product->getAttributeCombinationsById($returnProduct->id_product_attribute, $this->context->language->id);

                $dataProducts[] = [
                    'id' => $returnProduct->id_product,
                    'variationId' => $returnProduct->id_product_attribute,
                    'quantity' => $returnProduct->quantity,
                    'name' => (new Product($returnProduct->id_product))->name[$this->context->language->id],
                    'attributes' => array_map(static function ($attr) {
                        return [
                            'label' => $attr['group_name'],
                            'value' => $attr['attribute_name'],
                        ];
                    }, $productAttributes),
                ];
            }

            $data = [
                'id' => $return->id,
                'dateCreated' => $return->date_add,
                'dateUpdated' => $return->date_upd,
                'products' => $dataProducts,
                'status' => $return->status,
                'amount' => $return->amount,
            ];

            $context[] = $data;
        }

        $this->exitWithResponse($context);
    }

    public function ajaxProcessDoReturn(): void
    {
        $amountService = new AmountService();
        $client = new PayEyeHttpClient($this->module->config);

        try {
            $returnId = Tools::getValue('returnId');
            $amount = Tools::getValue('amount');

            $returnEntity = new PayEyeOrderReturn($returnId);

            if ($returnEntity->status !== ReturnStatus::CREATED) {
                $this->exitWithResponse([]);
            }

            $return = ReturnStatusRequest::builder()
                ->setShopIdentifier($this->module->authConfig->getShopId())
                ->setReturnId($returnId)
                ->setStatus(ReturnStatus::ACCEPTED)
                ->setAmount($amountService->convertFloatToInteger((float) $amount));

            $auth = AuthService::create(
                HashService::create($this->module->authConfig),
                SignatureFrom::RETURN_STATUS_REQUEST,
                $return->toArray()
            );

            $client->returnStatus($return, $auth);

            $returnEntity->status = TransferStatus::IN_PROGRESS;
            $returnEntity->amount = $amount;
            $returnEntity->update();

            $this->exitWithResponse([]);
        } catch (PrestaShopDatabaseException|PrestaShopException|HttpNetworkException $exception) {
            $this->exitWithExceptionMessage($exception);
        } catch (HttpException $exception) {
            $this->exitWithResponse(
                [
                    'message' => $exception->getMessage(),
                    'errorCode' => $exception->getMessage() ? JsonHelper::jsonDecode($exception->getMessage())['errorCode'] : null,
                ],
                400
            );
        }
    }

    public function ajaxProcessGetReturn(): void
    {
        try {
            $returnId = Tools::getValue('returnId');

            $return = new PayEyeOrderReturn($returnId);

            $this->exitWithResponse([
                'id' => $return->id,
                'amount' => $return->amount,
                'status' => $return->status,
            ]);
        } catch (PrestaShopDatabaseException|PrestaShopException $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }
}
