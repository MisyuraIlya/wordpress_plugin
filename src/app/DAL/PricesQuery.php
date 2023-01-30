<?php

namespace App\DAL;

require_once __DIR__ . '/../../../vendor/autoload.php';
use App\ResponseApi;
class PricesQuery
{
    function __construct($quries,$projectName,$data, $skip)
    {
        $this->skip = $skip;
        $this->data = $data;
        $this->projectName = $projectName;
        $this->dbQuery = $quries;
    }

    private function includePhpFile()
    {
        include __DIR__.'/../../plugins/'.$this->projectName.'/index.php';

    }

    public function response()
    {
        $this->includePhpFile();
        try{
            $res = apiSync($this->dbQuery,'GetPrices',$this->data,$this->skip,'','');

        } catch (\Throwable $e){
            (new ResponseApi(null,'erp connection faild'))->OnError();
            die();
        }
        return $res;

    }
}