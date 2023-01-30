<?php

namespace App\DAL;

require_once __DIR__ . '/../../../vendor/autoload.php';
use App\ResponseApi;

class CategoriesQuery
{
    function __construct($quries,$projectName,$data,$page)
    {
        $this->data = $data;
        $this->projectName = $projectName;
        $this->dbQuery = $quries;
        $this->page = $page;
    }


    private function includePhpFile()
    {
        include __DIR__.'/../../plugins/'.$this->projectName.'/index.php';

    }


    public function response()
    {
        $this->includePhpFile();
        try{
            $res = apiSync($this->dbQuery,'GetCategories',$this->data,$this->page,'','');

        } catch (\Throwable $e){
            (new ResponseApi(null,"erp connection faild"))->OnError();
            die();
        }
        return $res;
    }
}