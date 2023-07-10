<?php

namespace PrestaShop\Module\PayEye\Controller;

use PayEye\Lib\Enum\ErrorCode;
use PayEye\Lib\Enum\HttpStatus;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Tool\JsonHelper;

class AdminController extends \ModuleAdminController
{
    /** @var \PayEye */
    public $module;

    protected function exitWithResponse(array $data, int $httpCode = HttpStatus::OK): void
    {
        ob_end_clean();
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/json;charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        http_response_code($httpCode);
        echo JsonHelper::jsonEncode($data);
        exit;
    }

    /**
     * @param \Exception|\Throwable $exception
     *
     * @return void
     */
    protected function exitWithExceptionMessage($exception): void
    {
        $status = $exception instanceof \Exception ? HttpStatus::BAD_REQUEST : HttpStatus::INTERNAL_SERVER_ERROR;

        $this->exitWithResponse(
            [
                'signatureFrom' => SignatureFrom::HANDLE_ERROR,
                'errorMessage' => $exception->getMessage(),
                'errorCode' => ErrorCode::PRESTASHOP,
            ],
            $status
        );
    }
}
