<?php

declare(strict_types=1);

use PayEye\Lib\Enum\SignatureFrom;
use PayEye\Lib\Exception\CartNotFoundException;
use PayEye\Lib\Exception\InvalidCouponException;
use PayEye\Lib\Exception\PayEyePaymentException;
use PayEye\Lib\PromoCode\PromoCodeRequestModel;
use PayEye\Lib\PromoCode\PromoCodeResponseModel;
use PrestaShop\Module\PayEye\Controller\FrontController;

class PayEyePromoCodeModuleFrontController extends FrontController
{
    public function postProcess(): void
    {
        try {
            $request = $this->getRequest();
            $this->checkPermission($request);
            $request = PromoCodeRequestModel::createFromArray($request);
            $entity = PayEyeCartMapping::findByCartUuid($request->getCartId());

            if ($entity === null) {
                throw new CartNotFoundException();
            }

            $cart = new Cart($entity->id_cart);
            $this->context->cart = $cart;

            $id = CartRule::getIdByCode($request->getPromoCode());

            if ($id === false) {
                throw new InvalidCouponException();
            }

            $response = PromoCodeResponseModel::builder();

            if ($this->isRequestMethod('POST')) {
                $cartRule = new CartRule($id);

                if (Validate::isLoadedObject($cartRule) === false) {
                    throw new InvalidCouponException();
                }

                if ($cartRule->checkValidity($this->context, false, true)) {
                    throw new InvalidCouponException();
                }

                $cart->addCartRule((int) $id);
                $this->exitWithResponse($response->setSignatureFrom(SignatureFrom::CART_COUPON_APPLY_RESPONSE)->toArray());
            }

            if ($this->isRequestMethod('DELETE')) {
                $cart->removeCartRule((int) $id);
                $this->exitWithResponse($response->setSignatureFrom(SignatureFrom::CART_COUPON_APPLY_RESPONSE)->toArray());
            }
        } catch (PayEyePaymentException $exception) {
            $this->exitWithPayEyeExceptionResponse($exception);
        } catch (Exception|Throwable $exception) {
            $this->exitWithExceptionMessage($exception);
        }
    }
}
