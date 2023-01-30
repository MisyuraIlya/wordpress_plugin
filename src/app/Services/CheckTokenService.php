<?php

namespace App\Services;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\ConnectionDb;
use App\ResponseApi;
class CheckTokenService
{
    public function __construct($token)
    {
        $this->token = $token;
        $this->db = new ConnectionDb();
    }

    private function checkTokenValid()
    {
        $query = "SELECT * FROM tokens WHERE token = '$this->token'";
        $response = $this->db->dbQuery($query);
        return $response;
    }

    public function logic()
    {
        if(!empty($this->checkTokenValid())){
                return "token is valid";
        }
        else {
                (new ResponseApi(null,"token is not valid"))->OnError();
             die();

        }
    }


}