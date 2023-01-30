<?php

namespace App\ERP\SAP;

use GuzzleHttp\Client as Guzzle;

require_once __DIR__.'/../../../../vendor/autoload.php';

class SapConnection
{

    private $client;
    private string $session;

    public function __construct($url, $companyDb, $username, $password)
    {

        $this->url = $url;
        $this->company_db = $companyDb;
        $this->username = $username;
        $this->password = $password;

        $this->client = new Guzzle(['base_uri' => $this->url, 'verify' => false]);
        $body = [
            'CompanyDB' => $this->company_db,
            'UserName' => $this->username,
            'Password' => $this->password
        ];
        $headers = [
            'Content-type' => 'application/json',
        ];
        $response = $this->client->post($this->url . '/Login', [
            'headers' => $headers,
            'body' => json_encode($body)
        ]);
        $res = (string)$response->getBody();
        $decoded = json_decode($res, true);
        if (isset($decoded['SessionId'])) {
            $this->session = $decoded['SessionId'];
        }

    }

    public function __destruct()
    {
        if ($this->session) {
            $response = $this->client->post($this->url . '/Logout');
        }

    }

    public function SapGet(string $query, string $paramsQeury = null, string $maxpagesize = null)
    {

//        var_dump('param', $paramsQeury);
        if (isset($this->session)) {
            /*
                        $request_options = [
                            'headers' => [
                                'Cookie' => "B1SESSION={$this->session};ROUTEID=.node1;",
                                'Prefer' => "odata.maxpagesize=100",
                            ]
                        ];

                        $response = $this->client->request('GET', $this->url . $query, $request_options);
                        $data = $response->getBody()->getContents();
                        $res = json_decode($data, true);
                        $response->getBody()->close();
                        unset($response);

                        return $res;
                        unset($res);
            */
            $headr = array();
            $headr[] = 'Content-type: application/json';
            if ($maxpagesize) {
                $headr[] = 'Prefer:odata.maxpagesize=' . $maxpagesize;

            } else {
                $headr[] = 'Prefer:odata.maxpagesize=40';
            }
//            var_dump($this->url . $query . $paramsQeury);
            $ch = curl_init($this->url . $query . $paramsQeury);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
            curl_setopt($ch, CURLOPT_COOKIE, "B1SESSION={$this->session};ROUTEID=.node1;");

            $result = curl_exec($ch);
            //var_dump($this->url . $query);

            //var_dump($result);
            if (curl_errno($ch)) {
                //$error_msg = curl_error($ch);
//                var_dump('error');
                return null;
            } else {
                $response = json_decode($result, true);
            }
            curl_close($ch);

            return $response;
        }

        return null;

    }

    public function createUri(string $endpoint, string $query = null): string
    {
        $url = $this->url . '/' . $endpoint;
        if ($query != null) {
            $query = str_replace(' ', '%20', $query);
            $query = str_replace("'", '%27', $query);

            $url .= '?' . $query;
        }
        return $url;
    }

    public function SapPost(string $endpoint, string $data)
    {

        $request_options = [
            'headers' => [
                'Cookie' => "B1SESSION={$this->session};ROUTEID=.node1;",
                'Content-Type' => 'application/json'
            ],
            'body' => $data
        ];
        $response = $this->client->request('POST', $this->url . $endpoint, $request_options);
        $data = $response->getBody()->getContents();
        $res = json_decode($data, true);
        $response->getBody()->close();

        return $res;
    }

    public function SapPostCurl(string $endpoint, string $data)
    {

        $headr = array();
        $headr[] = 'Content-type: application/json';

        $ch = curl_init($this->url . $endpoint);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_COOKIE, "B1SESSION={$this->session};ROUTEID=.node1;");

        $res = curl_exec($ch);
        var_dump($res);
        return $res;
    }

    public function SapPatch(string $url, string $data)
    {
        $headers = [
            'Content-type' => 'application/json',
            'Content-Length' => strlen(json_encode($data))
        ];
        $response = $this->client->patch($this->url . '/Login', $headers, $data);
//        $response = $this->client->post($this->url.'/Login',['headers' => $headers, 'body' =>  $encodedBody]);
        print_r($response);
//        if (!$response){
//            throw new FaildToConnectSapServiceLayer;
//        }
        return $response;
    }
}

