<?php

namespace PayEye\Tests\Cart\Exceptions;

use PayEye\Lib\Auth\AuthConfig;
use PayEye\Lib\Exception\SignatureNotMatchedException;
use PayEye\Tests\Shared\BaseTestCase;

class SignatureNotMatchedExceptionTest extends BaseTestCase
{
    public function testSignatureNotMatchedException(): void
    {
        $authConfig = $this->module->authConfig;

        $this->module->authConfig = new AuthConfig('invalid-shop-id', 'invalid-api-key', 'invalid-secret-key');
        $this->getCart([
            'cartId' => '9e814eb4-9fe5-477f-aa96-0010ff3b4b11',
        ]);

        // restore authConfig
        $this->module->authConfig = $authConfig;

        $this->assertPayEyeException(new SignatureNotMatchedException());
    }
}
