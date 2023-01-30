
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

    public function __construct($query,$routerConnection)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    private function checkDuplicates(array $array,$id)
    {
        foreach ($array as $val){
            if($val->parentId == $id){
                return true;
            }
        }
        return false;

    }

    private function FetchOnlyParentProducts($data) :array
    {
        $ParentProducts = [];
        foreach ($data as $itemRec){
            if(get_object_vars($itemRec->Class2Name)[0] && !$this->checkDuplicates($ParentProducts, get_object_vars($itemRec->Class2)[0])){
                $obj = new stdClass();
                $obj->parentId = get_object_vars($itemRec->Class2)[0];
                $obj->parentTitle = get_object_vars($itemRec->Class2Name)[0];
                $ParentProducts[] = $obj;
            }

        }

        return $ParentProducts;
    }

    private function FetchOnlyChildVariations( $verifonData, array $data)
    {
        foreach ($data as &$itemRec){
            $itemRec->Variations = [];
            foreach ($verifonData as $verRec){
                if($itemRec->parentId == get_object_vars($verRec->Class2)[0]){

                    $obj = new stdClass();
                    $obj->code = get_object_vars($verRec->Code)[0];
                    $obj->name = get_object_vars($verRec->Name)[0];
                    $obj->description = get_object_vars($verRec->Description)[0];
                    $obj->main_category_id = get_object_vars($verRec->TypeID)[0];
                    $obj->sub_category_id = get_object_vars($verRec->Class1)[0];
                    $obj->price = get_object_vars($verRec->Price1)[0];
                    $obj->class3 = get_object_vars($verRec->Class3Name)[0];
                    $obj->class4 = get_object_vars($verRec->Class4Name)[0];
                    $obj->class5 = get_object_vars($verRec->Class5Name)[0];
                    $obj->class6 = get_object_vars($verRec->Class6Name)[0];
                    $obj->itemVat = get_object_vars($verRec->ItemVatPercent)[0];

                    $itemRec->Variations[] = $obj;

                }
            }
        }

        return $data;
    }

    private function FetchAttributesVerifone()
    {
        $data = $this->connection->VerifonePostRequest('GetAttributes','');
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductClassificationsResponse->GetProductClassificationsResult->Data->ProductClassifications->ProductClassification;
        $arrayData = [];
        foreach ($data as $itemRec){
//            var_dump($itemRec);
            $obj = new stdClass();
            $obj->id_attribute = get_object_vars($itemRec->Code)[0];
            $obj->title_attribute = get_object_vars($itemRec->Description)[0];
            $arrayData[] = $obj;
        }
        return $arrayData;
    }

    private  function  InsertAttributesToArray($mainArr, $AttributesArr)
    {
        foreach ($mainArr as &$itemRec){
            foreach ($itemRec->Variations as &$varRec){
                $array = [];

                foreach ($AttributesArr as $attRec){
                    if($varRec->class3 && $attRec->id_attribute == 13 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class3;

                        $array[] = $obj;
                    }
                    if($varRec->class4 && $attRec->id_attribute == 14 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class4;

                        $array[] = $obj;
                    }
                    if($varRec->class5 && $attRec->id_attribute == 15 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class5;

                        $array[] = $obj;
                    }
                    if($varRec->class6 && $attRec->id_attribute == 16 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class6;

                        $array[] = $obj;
                    }
                }

                $varRec->attributes = $array;

            }

        }

        return $mainArr;
    }


    private function checkDuplicate($array, $value)
    {
        foreach ($array as $val){
            if($val == $value){
                return true;
            }
        }
        return false;
    }
    private function fetchGlobalAttribute($array)
    {
        $result = null;
        $names = null;



        foreach ($array as $varRec){
            $data = [];
            $name = [];
            foreach ($varRec->attributes as $attRec){
                if(!$this->checkDuplicate($name,$attRec->name)){
                    $name[] = $attRec->name;
                }
            }
            $names = $name;
        }



        foreach ($names as $nameRec){
            $obj = new stdClass();
            $obj->name = $nameRec;
            $obj->visible = 1;
            $obj->variation = 1;
            $options = [];
            foreach ($array as $varRec){
                foreach ($varRec->attributes as $attRec){
                    if($nameRec == $attRec->name && !$this->checkDuplicate($options, $attRec->options)){
                        $options[] = $attRec->options;
                    }
                }
            }
            $obj->options = $options;
            $result[] = $obj;
        }




        return $result;
    }
    public function sync()
    {
        $data = $this->connection->VerifonePostRequest('GetProducts','');
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductsByPropertiesResponse->GetProductsByPropertiesResult->Data->Product;
        $parentProducts = $this->FetchOnlyParentProducts($data);
        $FetchChildVariations = $this->FetchOnlyChildVariations($data,$parentProducts);
        $attributesArray = $this->FetchAttributesVerifone();
        $insetAttributesToArray = $this->InsertAttributesToArray($FetchChildVariations,$attributesArray);

//        var_dump($insetAttributesToArray);


        $resultObj = [];
        foreach ($insetAttributesToArray as $itemRec)
        {
//            var_dump(get_object_vars($itemRec->Code)[0] );
            $variationArr = [];
            $defaultVariation = null;
            $fatherAttributes = null;
            $categoryId = null;


            $globaArrAtt = $this->fetchGlobalAttribute($itemRec->Variations);
            foreach ($itemRec->Variations as $key => $varRec){

                if($varRec->attributes && $fatherAttributes == null){

                    $categoryId = $varRec->sub_category_id;
                }
                $dto = (new ProdcutsDTO(
                    $varRec->code,
                    $varRec->name ?? null,
                    null,
                    $varRec->sub_category_id,
                    $varRec->description,
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
                    $varRec->attributes,
                    null

                ))->createObj();
                $variationArr[] = $dto;
                if($key == 1){
                    $defaultVariation = $varRec->attributes;
                }
            }

            $dto = (new ProdcutsDTO(
                $itemRec->parentId,
                $itemRec->parentTitle ?? null,
                null,
                $categoryId,
                null,
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

                $variationArr,
                $globaArrAtt,
                $defaultVariation

            ))->createObj();
            $resultObj[] = $dto;
        }
        return $resultObj;
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
//            var_dump('1');
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
    }
}

class GetAttributes
{
    public function __construct($query,$routerConnection)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
    }

    private function FetchAttributesVerifone()
    {
        $data = $this->connection->VerifonePostRequest('GetAttributes','');
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductClassificationsResponse->GetProductClassificationsResult->Data->ProductClassifications->ProductClassification;
        $arrayData = [];
        foreach ($data as $itemRec){
//            var_dump($itemRec);
            $obj = new stdClass();
            $obj->id_attribute = get_object_vars($itemRec->Code)[0];
            $obj->title_attribute = get_object_vars($itemRec->Description)[0];
            $arrayData[] = $obj;
        }
        return $arrayData;
    }

    private function CreateObjectWithAttributeName($data,$attributesArray)
    {
        $globalArr = [];
        foreach ($data as $itemRec){
            $objData = new stdClass();
            $objData->id_erp = get_object_vars($itemRec->Code)[0];

            $array = [];

            foreach ($attributesArray as $attRec){
                if($itemRec->Class3 && $attRec->id_attribute == 13 ){
                    $obj = new stdClass();
                    $obj->id =  $attRec->id_attribute;
                    $obj->name = $attRec->title_attribute;
                    $obj->options = get_object_vars($itemRec->Class3Name)[0];
                    $array[] = $obj;
                }
                if($itemRec->Class4 && $attRec->id_attribute == 14 ){
                    $obj = new stdClass();
                    $obj->id =  $attRec->id_attribute;
                    $obj->name = $attRec->title_attribute;
                    $obj->options = get_object_vars($itemRec->Class4Name)[0];
                    $array[] = $obj;
                }
                if($itemRec->Class5 && $attRec->id_attribute == 15 ){
                    $obj = new stdClass();
                    $obj->id =  $attRec->id_attribute;
                    $obj->name = $attRec->title_attribute;
                    $obj->options = get_object_vars($itemRec->Class5Name)[0];
                    $array[] = $obj;
                }
                if($itemRec->Class6 && $attRec->id_attribute == 16 ){
                    $obj = new stdClass();
                    $obj->id =  $attRec->id_attribute;
                    $obj->name = $attRec->title_attribute;
                    $obj->options = get_object_vars($itemRec->Class6Name)[0];
                    $array[] = $obj;
                }
//                var_dump($array);
//                $data[] = $array;
            }

            $objData->options = $array;
            $globalArr[] = $objData;
        }
        return $globalArr;

    }

    private function checkDuplicate($array, $title) {
        foreach ($array as $item) {
            if ($item == $title) {
                return true;
            }
        }
        return false;
    }
    private function createObjectByAttribute($data,$attributesArray)
    {
        $globalArr = [];
        foreach ($attributesArray as $attRec){
            $obj = new stdClass();
            $obj->name = $attRec->title_attribute;
            $optionsArray = [];
            foreach ($data as $itemRec){
                if($itemRec->Class3 && $attRec->id_attribute == 13 ){
                    if(get_object_vars($itemRec->Class3Name)[0]){
                        if(!$this->checkDuplicate($optionsArray,get_object_vars($itemRec->Class3Name)[0])){
                            $optionsArray[] = get_object_vars($itemRec->Class3Name)[0];
                        }

                    }
                }
                if($itemRec->Class4 && $attRec->id_attribute == 14 ){
                    if( get_object_vars($itemRec->Class4Name)[0]){
                        if(!$this->checkDuplicate($optionsArray,get_object_vars($itemRec->Class4Name)[0])){
                            $optionsArray[] = get_object_vars($itemRec->Class4Name)[0];
                        }
                    }
                }
                if($itemRec->Class5 && $attRec->id_attribute == 15 ){
                    if(get_object_vars($itemRec->Class5Name)[0]){
                        if(!$this->checkDuplicate($optionsArray,get_object_vars($itemRec->Class5Name)[0])){
                            $optionsArray[] = get_object_vars($itemRec->Class5Name)[0];
                        }
                    }
                }
                if($itemRec->Class6 && $attRec->id_attribute == 16 ){
                    if(get_object_vars($itemRec->Class6Name)[0]){
                        if(!$this->checkDuplicate($optionsArray,get_object_vars($itemRec->Class6Name)[0])){
                            $optionsArray[] = get_object_vars($itemRec->Class6Name)[0];
                        }
                    }
                }
            }
            $obj->options = $optionsArray;
            if(!empty($obj->options)){
                $globalArr[] = $obj;

            }
        }
        return $globalArr;
    }
    public function sync()
    {
        $data = $this->connection->VerifonePostRequest('GetProducts','');
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductsByPropertiesResponse->GetProductsByPropertiesResult->Data->Product;
//            foreach ($data as $itemRec){
//                var_dump(get_object_vars($itemRec->Class2)[0]);
//            }
        $attributesArray = $this->FetchAttributesVerifone();

//        $newArr = $this->CreateObjectWithAttributeName($data,$attributesArray);
        $newArr = $this->createObjectByAttribute($data,$attributesArray);
//        var_dump($newArr);
//        $resultObj = [];
//        foreach ($data as $itemRec)
//        {
//            $dto = (new Attributes(
//                get_object_vars($itemRec->Code)[0],
//                get_object_vars($itemRec->Name)[0],
//                get_object_vars($itemRec->Name)[0]
//            ))->createObj();
//            $resultObj[] = $dto;
//        }
        return $newArr;
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

class FilterByProduct
{
    public function __construct($query,$routerConnection,$sku)
    {
        $this->connection = $routerConnection;
        $this->query = $query;
        $this->sku = $sku;
//        var_dump($this->sku);
    }

    private function checkDuplicates(array $array,$id)
    {
        foreach ($array as $val){
            if($val->parentId == $id){
                return true;
            }
        }
        return false;

    }

    private function FetchOnlyParentProducts($data) :array
    {
        $ParentProducts = [];
        foreach ($data as $itemRec){
            if(get_object_vars($itemRec->Class2Name)[0] && !$this->checkDuplicates($ParentProducts, get_object_vars($itemRec->Class2)[0])){
                $obj = new stdClass();
                $obj->parentId = get_object_vars($itemRec->Class2)[0];
                $obj->parentTitle = get_object_vars($itemRec->Class2Name)[0];
                $ParentProducts[] = $obj;
            }

        }

        return $ParentProducts;
    }

    private function FetchOnlyChildVariations( $verifonData, array $data)
    {
        foreach ($data as &$itemRec){
            $itemRec->Variations = [];
            foreach ($verifonData as $verRec){
                if($itemRec->parentId == get_object_vars($verRec->Class2)[0]){

                    $obj = new stdClass();
                    $obj->code = get_object_vars($verRec->Code)[0];
                    $obj->name = get_object_vars($verRec->Name)[0];
                    $obj->description = get_object_vars($verRec->Description)[0];
                    $obj->main_category_id = get_object_vars($verRec->TypeID)[0];
                    $obj->sub_category_id = get_object_vars($verRec->Class1)[0];
                    $obj->price = get_object_vars($verRec->Price1)[0];
                    $obj->parent = get_object_vars($verRec->Class2)[0];
                    $obj->class3 = get_object_vars($verRec->Class3Name)[0];
                    $obj->class4 = get_object_vars($verRec->Class4Name)[0];
                    $obj->class5 = get_object_vars($verRec->Class5Name)[0];
                    $obj->class6 = get_object_vars($verRec->Class6Name)[0];
                    $obj->itemVat = get_object_vars($verRec->ItemVatPercent)[0];

                    $itemRec->Variations[] = $obj;

                }
            }
        }

        return $data;
    }

    private function FetchAttributesVerifone()
    {
        $data = $this->connection->VerifonePostRequest('GetAttributes','');
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductClassificationsResponse->GetProductClassificationsResult->Data->ProductClassifications->ProductClassification;
        $arrayData = [];
        foreach ($data as $itemRec){
//            var_dump($itemRec);
            $obj = new stdClass();
            $obj->id_attribute = get_object_vars($itemRec->Code)[0];
            $obj->title_attribute = get_object_vars($itemRec->Description)[0];
            $arrayData[] = $obj;
        }
        return $arrayData;
    }

    private  function  InsertAttributesToArray($mainArr, $AttributesArr)
    {
        foreach ($mainArr as &$itemRec){
            foreach ($itemRec->Variations as &$varRec){
                $array = [];

                foreach ($AttributesArr as $attRec){
                    if($varRec->class3 && $attRec->id_attribute == 13 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class3;
                        $array[] = $obj;
                    }
                    if($varRec->class4 && $attRec->id_attribute == 14 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class4;
                        $array[] = $obj;
                    }
                    if($varRec->class5 && $attRec->id_attribute == 15 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class5;
                        $array[] = $obj;
                    }
                    if($varRec->class6 && $attRec->id_attribute == 16 ){
                        $obj = new stdClass();
                        $obj->name = $attRec->title_attribute;
                        $obj->options = $varRec->class6;
                        $array[] = $obj;
                    }
                }

                $varRec->attributes = $array;

            }

        }

        return $mainArr;
    }

    private function checkIfSkuIsParent($insetAttributesToArray)
    {
        $isMatch = false;
        foreach ($insetAttributesToArray as $parentRec){
            if($parentRec->parentId == $this->sku){
                $isMatch = true;
            }
        }

        return $isMatch;
    }

    private function checkDuplicate($array, $value)
    {
        foreach ($array as $val){
            if($val == $value){
                return true;
            }
        }
        return false;
    }
    private function fetchGlobalAttribute($array)
    {
        $result = null;
        $names = null;



        foreach ($array as $varRec){
            $data = [];
            $name = [];
            foreach ($varRec->attributes as $attRec){
                if(!$this->checkDuplicate($name,$attRec->name)){
                    $name[] = $attRec->name;
                }
            }
            $names = $name;
        }



        foreach ($names as $nameRec){
            $obj = new stdClass();
            $obj->name = $nameRec;
            $obj->visible = 1;
            $obj->variation = 1;
            $options = [];
            foreach ($array as $varRec){
                foreach ($varRec->attributes as $attRec){
                    if($nameRec == $attRec->name && !$this->checkDuplicate($options, $attRec->options)){
                        $options[] = $attRec->options;
                    }
                }
            }
            $obj->options = $options;
            $result[] = $obj;
        }




        return $result;
    }

    private function findSubProduct($insetAttributesToArray)
    {
        $resultObj = [];
        foreach ($insetAttributesToArray as $itemRec)
        {
            $parent = null;
            $variationArr = [];
            $defaultVariation = null;
            $fatherAttributes = null;
            $categoryId = null;
            $globaArrAtt = $this->fetchGlobalAttribute($itemRec->Variations);

            foreach ($itemRec->Variations  as $key => $varRec) {
                if ($varRec->attributes && $fatherAttributes == null) {
                    $categoryId = $varRec->sub_category_id;
                }
                if($varRec->code == $this->sku){

                    $parent = $varRec->parent;
                    $dto = (new ProdcutsDTO(
                        $varRec->code,
                        $varRec->name ?? null,
                        null,
                        $varRec->sub_category_id,
                        $varRec->description,
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
                        $varRec->attributes,
                        null

                    ))->createObj();
                    $variationArr[] = $dto;


                }
                if($key == 1){
                    $defaultVariation = $varRec->attributes;
                }

            }
            if($parent == $itemRec->parentId){
                $dto = (new ProdcutsDTO(
                    $itemRec->parentId,
                    $itemRec->parentTitle ?? null,
                    null,
                    $categoryId,
                    null,
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
                        null,

                    ),

                    $variationArr,
                    $globaArrAtt,
                    $defaultVariation

                ))->createObj();
                $resultObj[] = $dto;
            }

        }
        return $resultObj;
    }

    private function findParentProductWithChildren($insetAttributesToArray)
    {
        foreach ($insetAttributesToArray as $itemRec)
        {
            $variationArr = [];
            $defaultVariation = null;
            $fatherAttributes = null;
            $categoryId = null;


            $globaArrAtt = $this->fetchGlobalAttribute($itemRec->Variations);
            foreach ($itemRec->Variations as $key => $varRec){

                if($varRec->attributes && $fatherAttributes == null){

                    $categoryId = $varRec->sub_category_id;
                }
                $dto = (new ProdcutsDTO(
                    $varRec->code,
                    $varRec->name ?? null,
                    null,
                    $varRec->sub_category_id,
                    $varRec->description,
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
                    $varRec->attributes,
                    null

                ))->createObj();
                $variationArr[] = $dto;
                if($key == 1){
                    $defaultVariation = $varRec->attributes;
                }
            }
            if($itemRec->parentId == $this->sku) {
                $dto = (new ProdcutsDTO(
                    $itemRec->parentId,
                    $itemRec->parentTitle ?? null,
                    null,
                    $categoryId,
                    null,
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

                    $variationArr,
                    $globaArrAtt,
                    $defaultVariation

                ))->createObj();
                $resultObj[] = $dto;
            }
        }
        return $resultObj;
    }
    public function sync()
    {
        $data = $this->connection->VerifonePostRequest('GetProducts','');
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $data);
        $xml = simplexml_load_string($clean_xml);
        $data = $xml->Body->GetProductsByPropertiesResponse->GetProductsByPropertiesResult->Data->Product;
        $parentProducts = $this->FetchOnlyParentProducts($data);
        $FetchChildVariations = $this->FetchOnlyChildVariations($data,$parentProducts);
        $attributesArray = $this->FetchAttributesVerifone();
        $insetAttributesToArray = $this->InsertAttributesToArray($FetchChildVariations,$attributesArray);

//        var_dump($insetAttributesToArray);

        $checkIsParent = $this->checkIfSkuIsParent($insetAttributesToArray);
//        var_dump($checkIsParent);
        if($checkIsParent){
            $object = $this->findParentProductWithChildren($insetAttributesToArray);
        } else {
            $object = $this->findSubProduct($insetAttributesToArray);

        }


        return $object;

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
        