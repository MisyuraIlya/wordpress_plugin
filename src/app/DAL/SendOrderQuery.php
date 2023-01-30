<?php

namespace App\DAL;

use App\ResponseApi;

require_once __DIR__ . '/../../../vendor/autoload.php';

class SendOrderQuery
{
    function __construct($quries,$projectName,$data,$json)
    {
        $this->data = $data;
        $this->projectName = $projectName;
        $this->dbQuery = $quries;
        $this->json = $json;
    }


    private function includePhpFile()
    {
        include __DIR__.'/../../plugins/'.$this->projectName.'/index.php';

    }


    public function response()
    {
        try {
            $this->includePhpFile();
            $res = apiSync($this->dbQuery,'SendOrder',$this->data,'', $this->json,'');
        } catch (\Throwable $e){
            (new ResponseApi(null,'erp connection faild'))->OnError();
            die();
        }

        return $res;
    }
}