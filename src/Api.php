<?php

namespace PrestaShop\Module\PayEye;

trait Api
{
    public function getRequest()
    {
        $body = \Tools::file_get_contents('php://input');
        $data = trim($body);

        return json_decode($data, true);
    }
}
