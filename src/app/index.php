<?php
require_once __DIR__ . '/../../vendor/autoload.php';
header("Content-Type: application/json");
use App\ConnectionDb;
use App\Exceptions\NoTokenFoundException;
use App\Controllers\ProductsController;
use App\Controllers\CategoriesController;
use App\Controllers\PricesController;
use App\Controllers\StocksController;
use App\Controllers\CheckTokenController;
use App\Controllers\SendOrderController;
use App\Controllers\AttributesController;
use App\ResponseApi;
error_reporting(E_ERROR | E_PARSE);
class GlobalApi
{

    function __construct(string $token, string $funcName,$page,$json,$sku) {

        $this->db = new ConnectionDb();
        $this->token = $token;
        $this->funcName = $funcName;
        $this->page = $page;
        $this->json = $json;
        $this->sku = $sku;
    }

    private function fetchData()
    {
        $obj = new stdClass();
        $data = $this->db->dbQuery("SELECT * FROM tokens WHERE token = '$this->token'");
        if(!$data){

            (new ResponseApi(null,'token is not valid'))->OnError();
            die();
        }

        return $data;

    }

    private function ExecuteController($data,$json)
    {
        switch ($this->funcName){
            case 'GetProducts':
                $result = new ProductsController($data,$this->page, $this->sku);
            break;
            case 'GetCategories':
                $result = new CategoriesController($data,$this->page);
            break;
            case 'GetPrices':
                $result = new PricesController($data,$this->page, $this->sku);
            break;
            case 'GetStocks':
                $result = new StocksController($data, $this->page, $this->sku);
            break;
            case 'CheckToken':
                $result = new CheckTokenController($this->token);
            break;
            case 'SendOrder':
                $result = new SendOrderController($data,$json);
            break;
            case 'GetAttributes':
                $result = new AttributesController($data,$json, $this->sku);
            break;

        }
        return $result;
    }

    public function response()
    {
        $dbData = $this->fetchData();
        $json = $this->json;
        if($dbData->active){
            try {
                $funcName = $this->ExecuteController($dbData,$json);
                $result = $funcName->result();
                (new ResponseApi($result,null))->OnSuccess();
            } catch (Throwable $e){
                (new ResponseApi(null,'function not exist or json not correct check (pages and funcName must be included)'))->OnError();
                die();
            }

        }elseif ($dbData->result == 'error'){
            $encodedToJson = json_encode((array) $dbData);
            echo $encodedToJson;
        } else {
             (new ResponseApi(null,'User Is Not Active'))->OnError();
             die();
        }

    }
}
//echo('hello world');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $token =  json_decode($data)->token;
    $page = json_decode($data)->page ?? 0;
    $funcName =  json_decode($data)->funcName;
    $json = json_decode($data)->json ?? '';
    $sku = json_decode($data)->sku ?? '';
    $test = new GlobalApi($token, $funcName,$page,$json,$sku );
    $test->response();
} else {
     (new ResponseApi(null,'faild Request'))->OnError();
     die();
}
