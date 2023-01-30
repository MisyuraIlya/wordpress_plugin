<?php

namespace App\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Services\CheckTokenService;

class CheckTokenController
{

    public function __construct($token)
    {
        $this->token = $token;
    }
    public function result()
    {
        $result = (new CheckTokenService($this->token))->logic();

        return $result;

    }
}