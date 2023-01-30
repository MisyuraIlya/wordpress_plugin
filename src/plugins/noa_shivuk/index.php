<?php

require_once __DIR__ . "/../../../vendor/autoload.php";

use  App\ERP\ErpRouter;
use App\DTO\ProdcutsDTO;
use App\DTO\CategoryIds;
use App\DTO\Attributes;
use App\DTO\Variation;
use App\DTO\SubVariationCustomFields;
use App\DTO\CustomFields;
use App\DTO\CategoriesDTO;
use App\DTO\PricesDTO;
use App\DTO\StocksDTO;

class GetProducts{

    public function __construct($query,$routerConnection,$page)
    {
        $this->page = $page;
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    public function sync1()
    {





                $data2 = (new ProdcutsDTO(
                    "",
                    "name products",
                    "description of product",
                    "short description of product",
                    "image link of product",
                    new CategoryIds(1),
                    new Attributes("size",["s","L","XL","XXL"],1,1),
                    new Variation(
                        "image link",
                        10,
                        "sku",
                        15,
                        10,
                        "description",
                        ["color"=>"red","size"=>"S"],
                        10,
                        10,
                        10,
                        10,
                        new SubVariationCustomFields(
                            "bardoce",
                            [1,322],
                            "promotion",
                            ["1"=>"value1","2"=>"value2","3"=>"value3"],
                            ["1"=>"value1","2"=>"value2","3"=>"value3"],
                            "2022-11-22",
                            "l",
                            43.6,
                            13,
                            "yes",
                            "yes",
                            "characteristics",
                            ["image link1", "image link2"],
                        ),
                        36.4,
                        "yes",
                        "no"
                    ),
                    ["Color"=>"white","size"=>"XXL"],
                    new CustomFields(
                        "1",
                        "information delivry",
                        10,
                        "2022-11-22",
                        "packing characteristicks"
                    ),
                    "visibile"



                ));


        $data = (new ProdcutsDTO(
            "1",
            "name products",
            "description of product",
            "short description of product",
            "image link of product",
            new CategoryIds(1),
            new Attributes("size",["s","L","XL","XXL"],1,1),
            new Variation(
                "image link",
                10,
                "sku",
                15,
                10,
                "description",
                ["color"=>"red","size"=>"S"],
                10,
                10,
                10,
                10,
                new SubVariationCustomFields(
                    "bardoce",
                    [1,322],
                    "promotion",
                    ["1"=>"value1","2"=>"value2","3"=>"value3"],
                    ["1"=>"value1","2"=>"value2","3"=>"value3"],
                    "2022-11-22",
                    "l",
                    43.6,
                    13,
                    "yes",
                    "yes",
                    "characteristics",
                    ["image link1", "image link2"],
                ),
                36.4,
                "yes",
                "no"
            ),
            ["Color"=>"white","size"=>"XXL"],
            new CustomFields(
                "1",
                "information delivry",
                10,
                "2022-11-22",
                "packing characteristicks"
            ),
            "visibile"



        ));
        return $data;
    }

    private function SkipController(string $nextLink)
    {
        $skip = explode("skip=",$nextLink);
        $skip = $skip[1];
        $this->sync((string)$skip);
    }

    public function sync()
    {
        if($this->page == 0)
        {
            $numberSkip = '&$skip=0' ;

        } else {
            $numberSkip = '&$skip=' . $this->page * 40 ;
        }
        $data = $this->connection->SapGet($this->query,$numberSkip);
        $skip = "";
        if(isset($data["@odata.nextLink"])){
            $skip = explode("skip=",$data["@odata.nextLink"]);
            $skip = $skip[1];
        }

        $resultObj = [];
        foreach ($data["value"] as $itemRec)
        {
            $dto = (new ProdcutsDTO(
                $itemRec["ItemCode"],
                $itemRec["ItemName"],
                $itemRec["ForeignName"],
                $itemRec["ItemsGroupCode"],

                
//                null,
                null,
                null,
                null,
                true,
                null,
                null,
                null,
                null,
                
               

                
                
                
                
                [],
                new CustomFields(
                    $itemRec["BarCode"],
                    $itemRec["VatLiable"] == "tYES" ? true : false ,
                    $itemRec["SalesItem"] == "tYES" ? true : false,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null

                ),
                []

            ))->createObj();
            $resultObj[] = $dto;
        }

        if($skip){
            $pageNumber = $this->page + 1;
        } else {
            $pageNumber = "";
        }


        $responseResult = new stdClass();
        $responseResult->data = $resultObj;
        $responseResult->next_page = $pageNumber;

        return $responseResult;

    }
}

class GetCategories{
    public function __construct($query,$routerConnection,$page)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
        $this->page = $page;
    }

    private function lvl1Query()
    {
        $query = "/SQLQueries('sql1')/List";
        $data = $this->connection->SapGet($query,"");
        $resultObj = [];
        foreach ($data["value"] as $itemRec)
        {
            $dto = (new CategoriesDTO(
                $itemRec["FldValue"],
                $itemRec["Descr"],
                null,
                null,
                1,
                null,
                "visible"
            ))->createObj();
            $resultObj[] = $dto;
        }
        return $resultObj;

    }

    private function lvl2Query($skip)
    {
        $data = $this->connection->SapGet($this->query,$skip);
        $skip = "";

        $resultObj = [];
        foreach ($data["value"] as $itemRec)
        {
            $dto = (new CategoriesDTO(
                $itemRec["Number"],
                $itemRec["GroupName"],
                null,
                null,
                2,
                $itemRec["U_HighGrp"],
                "visible"
            ))->createObj();
            $resultObj[] = $dto;
        }

        if(isset($data["@odata.nextLink"])){
            $skip = $this->nextLink($data["@odata.nextLink"]);
        }

        return [$resultObj,$skip];

    }

    private function nextLink($link)
    {
        $skip = explode("skip=",$link);
        $skip = $skip[1];
        return $skip;
    }

    public function sync()
    {
        if($this->page == 0)
        {
            $numberSkip = '&$skip=0' ;

        } else {
            $numberSkip = '&$skip=' . $this->page * 40 ;
        }

        if($this->page == 0){
            $firstCategory = $this->lvl1Query();
        }
        $secondCategory = $this->lvl2Query($numberSkip);

        if($this->page == 0){
            $data = array_merge($firstCategory, $secondCategory[0]);
        } else {
            $data = $secondCategory[0];
        }

        if($secondCategory[1]){
            $pageNumber = $this->page + 1;
        } else {
            $pageNumber = "";
        }
        $responseResult = new stdClass();
        $responseResult->data = $data;
        $responseResult->next_page = $pageNumber;

        return $responseResult;

    }
}

class GetPrices{
    public function __construct($query,$routerConnection, $page)
    {
        $this->page = $page;
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    public function sync()
    {

        if($this->page == 0)
        {
            $numberSkip = '?$skip=0' ;

        } else {
            $numberSkip = '?$skip=' . $this->page * 40 ;
        }
        $data = $this->connection->SapGet($this->query,$numberSkip);
        $skip = "";

        if(isset($data["@odata.nextLink"])){
            $skip = explode("skip=",$data["@odata.nextLink"]);
            $skip = $skip[1];
        }

        $resultObj = [];
        foreach ($data["value"] as $itemRec)
        {
            $dto = (new PricesDTO(
                $itemRec["ItemCode"],
                $itemRec["NoaPrice"]

            ))->createObj();
            $resultObj[] = $dto;
        }

        if($skip){
            $pageNumber = $this->page + 1;
        } else {
            $pageNumber = "";
        }

        $responseResult = new stdClass();
        $responseResult->data = $resultObj;
        $responseResult->next_page = $pageNumber;

        return $responseResult;


    }
}

class GetStocks
{
    public function __construct($query,$routerConnection, $page)
    {
        $this->page = $page;
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    public function sync()
    {
        if($this->page == 0)
        {
            $numberSkip = '?$skip=0' ;

        } else {
            $numberSkip = '?$skip=' . $this->page * 40 ;
        }
        $data = $this->connection->SapGet($this->query,$numberSkip);
        $skip = "";

        if(isset($data["@odata.nextLink"])){
            $skip = explode("skip=",$data["@odata.nextLink"]);
            $skip = $skip[1];
        }

        $resultObj = [];
        foreach ($data["value"] as $itemRec)
        {
            $dto = (new StocksDTO(
                $itemRec["ItemCode"],
                $itemRec["OnHand"]

            ))->createObj();
            $resultObj[] = $dto;
        }

        if($skip){
            $pageNumber = $this->page + 1;
        } else {
            $pageNumber = "";
        }

        $responseResult = new stdClass();
        $responseResult->data = $resultObj;
        $responseResult->next_page = $pageNumber;

        return $responseResult;


    }
}

class SendOrder
{
    public function __construct($query,$routerConnection, $json)
    {
        $this->json = $json;
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    private function createOrderLine($headerLine)
    {
//        $order = new stdClass;
//        $order->CardCode = $history->getExId();
//        $order->Series = 5;
//        $order->DocDate = date("Y-m-d");
//        $order->DocDueDate = $history->getRequestedDate() ? $history->getRequestedDate() : date("Y-m-d");
//        $order->Comments = "";
//        return $order;
    }

    private function createDocumentLines($prodLine)
    {
//        $documentLines = [];
//        $products = HistoryDetailedQuery::create()->filterByHistoryId($id)->find();
//        foreach ($products as $key => $product)
//        {
//            $productLine = new stdClass;
//            $productLine->ItemCode = $product->getCatalogNum();
//            $productLine->Quantity = strval(intval($product->getQuantity()));
//            $productLine->UnitPrice = strval(floatVal($product->getSinglePrice()));
//            //$productLine->BarCode = $product->getBarcode();
//            $productLine->UseBaseUnits = "tYES";
//            $documentLines[] = $productLine;
//        }
//        return $documentLines;
    }

    public function sync()
    {

//        $object = $this->json;
//        var_dump($object);

//        $headerLine = $object->


//        $response = $this->connection->SapPost($this->query,$object);




//        $resultObj = [];
//        foreach ($data["value"] as $itemRec)
//        {
//            $dto = (new StocksDTO(
//                $itemRec["ItemCode"],
//                $itemRec["OnHand"]
//
//            ))->createObj();
//            $resultObj[] = $dto;
//        }



//        return $responseResult;
        return "success";

    }

}


function apiSync($query,$func,$data,$page,$json)
{

//    $routerConnection = (new ErpRouter())->GetConnection($data);

    switch ($func){
        case "GetProducts";
            $data = (new GetProducts($query,$routerConnection,$page))->sync();
            break;
        case "GetCategories";
            $data = (new GetCategories($query,$routerConnection,$page))->sync();
            break;
        case "GetPrices";
            $data =(new GetPrices($query,$routerConnection,$page))->sync();
            break;
        case "GetStocks";
            $data =(new GetStocks($query,$routerConnection,$page))->sync();
            break;
        case "SendOrder";
            $data = (new SendOrder($query,$routerConnection,$json))->sync();
            break;
    }

    return $data;
}

?>