<?php

namespace App\ERP\verifone;

use GuzzleHttp\Client as Guzzle;
use App\DTO\CategoriesDTO;
use SimpleXMLElement;
use SoapClient;
use App\ResponseApi;
Header('Content-type: text/xml');

require_once __DIR__.'/../../../../vendor/autoload.php';

class VerifoneConnection
{
    public function __construct($url, $companyDb, $username, $password)
    {
        $this->url = $url;
        $this->company_db = $companyDb;
        $this->username = $username;
        $this->password = $password;
        $this->client = new Guzzle(['base_uri' => $this->url, 'verify' => false]);
    }



    private function SoapProducts()
    {
        $xml = '
                 <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
                   <soap:Header/>
                   <soap:Body>
                      <tem:GetProductsByProperties>
                         <!--Optional:-->
                         <tem:User>
                            <tem:ChainID>'. $this->company_db .'</tem:ChainID>
                            <!--Optional:-->
                            <tem:Username>'. $this->username .'</tem:Username>
                            <!--Optional:-->
                            <tem:Password>'. $this->password .'</tem:Password>
                         </tem:User>
                         <!--Optional:-->
                         <tem:Request>
                            <!--Optional:-->
                            <tem:FromProductCode>1</tem:FromProductCode>
                            <!--Optional:-->
                            <tem:ToProductCode>99999999999999</tem:ToProductCode>
                            <!--Optional:-->
                         </tem:Request>
                      </tem:GetProductsByProperties>
                   </soap:Body>
                </soap:Envelope>
                ';


        return $xml;
    }


    private function SoapCategories()
    {
        $xml = '
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <GetProductTypes xmlns="http://tempuri.org/">
                      <User>
                        <ChainID>'. $this->company_db .'</ChainID>
                        <Username>'. $this->username .'</Username>
                        <Password>'. $this->password .'</Password>
                      </User>
                    </GetProductTypes>
                  </soap:Body>
                </soap:Envelope>
                ';


        return $xml;
    }

    private function FetchAttributesNames()
    {
        $xml = '
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
           <soap:Header/>
           <soap:Body>
              <tem:GetProductClassifications>
                 <!--Optional:-->
                 <tem:User>
                    <tem:ChainID>'. $this->company_db .'</tem:ChainID>
                    <!--Optional:-->
                    <tem:Username>'. $this->username .'</tem:Username>
                    <!--Optional:-->
                    <tem:Password>'. $this->password .'</tem:Password>
                 </tem:User>
                 <!--Optional:-->
                 <tem:Request>
                    <!--Optional:-->
                    <tem:FromProductClassificationType>1</tem:FromProductClassificationType>
                    <!--Optional:-->
                    <tem:ToProductClassificationType>99999999999999</tem:ToProductClassificationType>
                    <!--Optional:-->
                 </tem:Request>
              </tem:GetProductClassifications>
           </soap:Body>
        </soap:Envelope>
                ';


        return $xml;
    }

    private function FilterBySku($sku)
    {
        $xml = '
                <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <GetProducts xmlns="http://tempuri.org/">
                      <User>
                        <ChainID>'. $this->company_db .'</ChainID>
                        <Username>'. $this->username .'</Username>
                        <Password>'. $this->password .'</Password>
                      </User>
                      <Request>
                        <ProductHeaders>
                          <ProductHeader>
                            <Code>'. $sku .'</Code>
                          </ProductHeader>
                        </ProductHeaders>
                      </Request>
                    </GetProducts>
                  </soap:Body>
                </soap:Envelope>
                ';


        return $xml;
    }


    public function VerifonePostRequest($funName,$sku)
    {
        switch ($funName){
            case "GetProducts";
                $res = $this->SoapProducts();
                break;
            case "GetCategories";
                $res = $this->SoapCategories();
            case "GetPrices";
                break;
            case "GetAttributes";
                $res = $this->FetchAttributesNames();
                break;
            case "FilterBySku";
                $res = $this->FilterBySku($sku);
                break;
        }

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => $res,
//            'timeout' => 10.0
        ];

        try {
            $response = $this->client->request('POST', $this->url, $options);
            if( $response->getStatusCode() == 504){
                (new ResponseApi(null,"connection closed between ips"))->OnError();
                die();
            }
            $data = $response->getBody()->getContents();
            $response->getBody()->close();
            return $data;
        } catch (\Throwable $e){
            (new ResponseApi(null,"connection closed between ips"))->OnError();
            die();
        }


    }
}

