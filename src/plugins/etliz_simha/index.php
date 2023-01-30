<?php

require_once __DIR__ . "/../../../vendor/autoload.php";

use  App\ERP\ErpRouter;
use App\DTO\ProdcutsDTO;
use App\DTO\CategoriesDTO;
use App\DTO\PricesDTO;
use App\DTO\CustomFields;
use App\DTO\StocksDTO;
use App\ResponseApi;

class GetProducts{

    public function __construct($query,$routerConnection,$page)
    {
        $this->page = $page;
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    public function sync()
    {
        if($this->page == 0)
        {
            $data = $this->connection->VerifonePostRequest('GetProducts','');
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
            $xml = simplexml_load_string($clean_xml);
            $data = $xml->Body->GetProductsByPropertiesResponse->GetProductsByPropertiesResult->Data->Product;


            $resultObj = [];
            foreach ($data as $itemRec)
            {
//            var_dump(get_object_vars($itemRec->Code)[0] );
                $dto = (new ProdcutsDTO(
                    get_object_vars($itemRec->Code)[0],
                    get_object_vars($itemRec->Name)[0] ?? null,
                    null,
                    get_object_vars($itemRec->TypeID)[0],
                    null,
//                    get_object_vars($itemRec->Pric1)[0] ?? null,
//                    get_object_vars($itemRec->Stock)[0] ?? null,
                    null,
                    true,
                    null,
                    null,
                    null,
                    null,
                    null,
                    new CustomFields(
                        null,
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null
                    ),
                    null,
                    null,
                    null

                ))->createObj();
                $resultObj[] = $dto;
            }
            return $resultObj;

        } else {
            return [];
        }

    }
}

class GetCategories{


    public function __construct($query,$routerConnection)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    public function sync()
    {
        if($this->page == 0)
        {
            $data = $this->connection->VerifonePostRequest('GetCategories','');
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
            $xml = simplexml_load_string($clean_xml);
            $data = $xml->Body->GetProductTypesResponse->GetProductTypesResult->Data->ProductTypes->ProductType;
//        var_dump($data);

            $resultObj = [];
            foreach ($data as $itemRec)
            {
                $dto = (new CategoriesDTO(
                    get_object_vars($itemRec->Code)[0],
                    str_replace(' ','',get_object_vars($itemRec->Description)[0]),
                    null,
                    null,
                    1,
                    null,
                    true,
                ))->createObj();
                $resultObj[] = $dto;
            }
            return $resultObj;

        } else {
            return [];
        }


    }
}

class GetPrices{

    public function __construct($query,$routerConnection)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    public function sync()
    {

        if($this->page == 0)
        {
            $data = $this->connection->VerifonePostRequest('GetProducts','');
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
            $xml = simplexml_load_string($clean_xml);
            $data = $xml->Body->GetProductsByPropertiesResponse->GetProductsByPropertiesResult->Data->Product;

            $resultObj = [];
            foreach ($data as $itemRec)
            {
                $dto = (new PricesDTO(
                    get_object_vars($itemRec->Code)[0],
                    get_object_vars($itemRec->Price1)[0] ?? null
                ))->createObj();
                $resultObj[] = $dto;
            }
            return $resultObj;

        } else {
            return [];
        }

    }
}

class GetStocks
{
    public function __construct($query,$routerConnection)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
    }


    public function sync()
    {
        if($this->page == 0)
        {
            $data = $this->connection->VerifonePostRequest('GetProducts','');
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
            $xml = simplexml_load_string($clean_xml);
            $data = $xml->Body->GetProductsByPropertiesResponse->GetProductsByPropertiesResult->Data->Product;


            $resultObj = [];
            foreach ($data as $itemRec)
            {
                $dto = (new StocksDTO(
                    get_object_vars($itemRec->Code)[0],
                    get_object_vars($itemRec->Stock)[0] ?? null
                ))->createObj();
                $resultObj[] = $dto;
            }
            return $resultObj;

        } else {
            return [];
        }

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
//        $order->Comments = '';
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
//            $productLine->UseBaseUnits = 'tYES';
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
//        foreach ($data['value'] as $itemRec)
//        {
//            $dto = (new StocksDTO(
//                $itemRec['ItemCode'],
//                $itemRec['OnHand']
//
//            ))->createObj();
//            $resultObj[] = $dto;
//        }



//        return $responseResult;
        return "success";

    }

}

class GetAttributes
{

    public function sync()
    {

        (new ResponseApi(null,'There no attributes for this project'))->OnError();
        die;

    }
}

class FilterByProduct
{
    public function __construct($query,$routerConnection,$sku)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
        $this->sku = $sku;
    }

    public function sync()
    {
        if($this->page == 0)
        {
            $data = $this->connection->VerifonePostRequest('FilterBySku','');
            $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
            $xml = simplexml_load_string($clean_xml);
            $data = $xml->Body->GetProductsResponse->GetProductsResult->Data->Product;


            $resultObj = [];
            foreach ($data as $itemRec)
            {
//            var_dump(get_object_vars($itemRec->Code)[0] );
                $dto = (new ProdcutsDTO(
                    get_object_vars($itemRec->Code)[0],
                    get_object_vars($itemRec->Name)[0] ?? null,
                    null,
                    get_object_vars($itemRec->TypeID)[0],
                    null,
//                    get_object_vars($itemRec->Pric1)[0] ?? null,
//                    get_object_vars($itemRec->Stock)[0] ?? null,
                    null,
                    true,
                    null,
                    null,
                    null,
                    null,
                    null,
                    new CustomFields(
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null
                    ),
                    null,
                    null

                ))->createObj();
                $resultObj[] = $dto;
            }
            return $resultObj;

        } else {
            return [];
        }
    }
}

class FilterByPrice
{
    public function __construct($query,$routerConnection,$sku)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
        $this->sku = $sku;
    }


    public function sync()
    {
        $data = $this->connection->VerifonePostRequest('FilterBySku',$this->sku);
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductsResponse->GetProductsResult->Data->Product;
//        var_dump($data);
        $resultObj = [];
        foreach ($data as $itemRec)
        {
            $dto = (new PricesDTO(
                get_object_vars($itemRec->Code)[0],
                get_object_vars($itemRec->Price1)[0] ?? null
            ))->createObj();
            $resultObj[] = $dto;
        }
        return $resultObj;
    }
}

class FilterByStock
{
    public function __construct($query,$routerConnection,$sku)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
        $this->sku = $sku;
    }


    public function sync()
    {
        $data = $this->connection->VerifonePostRequest('FilterBySku',$this->sku);
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductsResponse->GetProductsResult->Data->Product;

        $resultObj = [];
        foreach ($data as $itemRec)
        {
            $dto = (new StocksDTO(
                $this->sku,
                get_object_vars($itemRec->Stock)[0] ?? null
            ))->createObj();
            $resultObj[] = $dto;
        }
        return $resultObj;
    }
}

function apiSync($query,$func,$data,$page,$json,$sku)
{

    $routerConnection = (new ErpRouter())->GetConnection($data);

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
        case "GetAttributes";
            $data = (new GetAttributes($query,$routerConnection,$json))->sync();
            break;
        case "FilterByProduct";
            $data = (new FilterByProduct($query,$routerConnection,$sku))->sync();
            break;
        case "FilterByPrice";
            $data = (new FilterByPrice($query,$routerConnection,$sku))->sync();
            break;
        case "FilterByStock";
            $data = (new FilterByStock($query,$routerConnection,$sku))->sync();
            break;
    }

    return $data;
}

?>
        