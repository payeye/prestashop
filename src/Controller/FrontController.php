<?php

namespace PrestaShop\Module\PayEye\Controller;

use PayEye\Lib\Auth\AuthRequest;
use PayEye\Lib\Auth\AuthService;
use PayEye\Lib\Auth\HashService;
use PayEye\Lib\Enum\ErrorCode;
use PayEye\Lib\Enum\HttpStatus;
use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\Exception\SignatureNotMatchedException;
use PayEye\Lib\Tool\JsonHelper;

class FrontController extends \ModuleFrontController
{
    /** @var \PayEye */
    public $module;

    protected function isRequestMethod(string $type): bool
    {
        return $_SERVER['REQUEST_METHOD'] === $type;
    }

    /**
     * @throws \Exception
     */
    protected function getRequest(): array
    {
        $body = \Tools::file_get_contents('php://input');
        $data = trim($body);

        json_decode($data, false);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \JsonException('Invalid JSON');
        }

        return JsonHelper::jsonDecode($data, true);
    }

    /**
     * @throws SignatureNotMatchedException
     */
    protected function checkPermission(array $request): void
    {
        if ($this->permission($request) === false) {
            throw new SignatureNotMatchedException();
        }
    }

    protected function exitWithPayEyeExceptionResponse(PayEyePaymentException $exception): void
    {
        $this->exitWithResponse([
            'signatureFrom' => SignatureFrom::HANDLE_ERROR,
            'errorMessage' => $exception->getMessage(),
            'errorCode' => $exception->getErrorCode(),
        ], $exception->getStatusCode());
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

    protected function exitWithResponse(?array $data, int $httpCode = HttpStatus::OK): void
    {
        if (isset($data['signatureFrom'])) {
            $data['signature'] = $this->getSignatureFromPayload($data);
        }

        ob_end_clean();
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/json;charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        http_response_code($httpCode);
        if ($data) {
            echo JsonHelper::jsonEncode($data);
        } else {
            echo $data;
        }

        exit;
    }

    private function getSignatureFromPayload(array $payload): string
    {
        $auth = new AuthService(
            new HashService($this->module->authConfig),
            $payload['signatureFrom'],
            $payload
        );

        return $auth->getSignature();
    }

    private function permission(array $request): bool
    {
        $authRequestModel = AuthRequest::createFromArray($request);

        $authService = new AuthService(
            new HashService($this->module->authConfig),
            $authRequestModel->getSignatureFrom(),
            $request
        );

        return $authService->getSignature() === $authRequestModel->getSignature();
    }
}
