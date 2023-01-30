<?php

namespace App\DAL;

use App\ResponseApi;

require_once __DIR__ . '/../../../vendor/autoload.php';
class FilterByQuery
{
    function __construct($quries,$projectName,$data, $page, $functionName, $sku)
    {
        $this->page = $page;
        $this->data = $data;
        $this->projectName = $projectName;
        $this->dbQuery = $quries;
        $this->functionName = $functionName;
        $this->sku = $sku;
    }

    private function includePhpFile()
    {
        include __DIR__.'/../../plugins/'.$this->projectName.'/index.php';

    }

    private function QueryRouter()
    {
        switch ($this->functionName) {
            case 'FilterByProduct':
                $result =apiSync($this->dbQuery,'FilterByProduct',$this->data,$this->page,'',$this->sku);
                break;
            case 'FilterByPrice':
                $result = apiSync($this->dbQuery,'FilterByPrice',$this->data,$this->page,'',$this->sku);
                break;
            case 'FilterByStock':
                $result = apiSync($this->dbQuery,'FilterByStock',$this->data,$this->page,'',$this->sku);
                break;
        }

        return $result;
    }

    public function response()
    {
        $this->includePhpFile();
        try{
            $res = $this->QueryRouter();

        }catch (\Throwable $e){
            (new ResponseApi(null,'erp connection faild'))->OnError();
            die();
        }
        return $res;

    }
}