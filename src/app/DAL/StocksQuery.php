<?php

namespace App\DAL;

use App\ResponseApi;

require_once __DIR__ . '/../../../vendor/autoload.php';

class StocksQuery
{
    function __construct($quries,$projectName,$data, $page)
    {
        $this->page = $page;
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
            $res = apiSync($this->dbQuery,'GetStocks',$this->data,$this->page,'','');

        }catch (\Throwable $e){
            (new ResponseApi(null,'erp connection faild'))->OnError();
            die();
        }
        return $res;

    }
}