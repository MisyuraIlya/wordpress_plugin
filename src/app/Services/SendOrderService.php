<?php

namespace App\Services;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\ConnectionDb;
use App\DAL\SendOrderQuery;
//ini_set('display_errors', 0);
class SendOrderService
{
    function __construct($data,$json)
    {

        $this->data = $data;
        $this->json = $json;
        $this->db = new ConnectionDb();
    }

    private function InsertJsonOrders()
    {

        $data = [];
        foreach ($this->json as $orderRec){
            $query = "INSERT INTO history_order (project_name, token, json) VALUES (?,?,?)";
            $stmt= $this->db->conn->prepare($query);
            $result = $stmt->execute([$this->data->project, $this->data->token, json_encode($orderRec)]);
            $id = $this->db->conn->lastInsertId();
            $obj = new \stdClass();
            $obj->result = $result ? 'success' : 'error';
            $obj->orderId = $id;
            $obj->wordpressOrder = $orderRec->Order->OrderNumber;
            $data[] =  $obj;
        }

        return $data;
    }

    private function fetchQuery($projectName)
    {
        $query = "SELECT * FROM query WHERE project_name = '$projectName'";
        $response = $this->db->dbQuery($query);
        return $response;
    }

    private function InsertHistoryDb()
    {
//        var_dump($this->json);
        foreach ($this->json as $historyRec){
            $query = "INSERT INTO history (project_name,order_number,status,total,discount,tax,currency,coupon,user_id,user_name,
                     user_lastname,user_email,user_phone,billing_status,payment_method,payments,credit_card_approve_number,
                     credit_card_j5_auth_number,credit_card_transaction_log,credit_card_transaction_number,	invoice_number,invoice_in_the_nam_of,
                     invoice_url,invoice_log,invoice_code,delivery_company_number,delivery_company_price,delivery_company_cost,delivery,
                     delivery_date,delivery_hours,delivery_number,delivery_price,pick_up_location,shipping_company_name,shipping_first_name,
                     shipping_last_name,shipping_address,shipping_street,shipping_street_number,shipping_apartmnet_number,shipping_floor_number,
                     shipping_city,shipping_state,shippng_country,shipping_zip,shipping_po_box,shipping_home_phone,shipping_work_phone,shipping_mobile_phone,
                     billing_address_company_name,billing_address_first_name,billing_address_last_name,billing_address_street,	billing_address_street_number,
                     billing_address_apartment_number,billing_address_floor_number,billing_address_city,billing_address_zip_code,billing_address_state,
                     billing_address_country,billing_address_home_phone,billing_address_work_phone,billing_address_mobile_phone,payment_info_id,payment_info_ccode,payment_info_amount,
                     payment_info_acode,payment_info_fild1,payment_info_bank,payment_info_payments,	payment_info_user_id,payment_info_brand,payment_info_issuer,payment_info_l4digit,
                     payment_info_street,payment_info_city,payment_info_zip,payment_info_cell,payment_info_coin,payment_info_tmonth,payment_info_tyear,
                     payment_info_info,	payment_info_error_msg
                     
                     ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt= $this->db->conn->prepare($query);
            $result = $stmt->execute([
                $this->data->project,

                ]);
//            var_dump($result);
        }
    }

    private function InsertHistoryDetailedDb()
    {

    }





    public function logic()
    {


//        var_dump($this->data);
        $this->InsertHistoryDb();
        $responseDB = $this->InsertJsonOrders();
        if($responseDB){
            $queries = $this->fetchQuery($this->data->project);
            $response = (new SendOrderQuery($queries->post_send_order,$queries->project_name,$this->data,$this->json))->response();
            if($response == 'success'){
                $obj = new \stdClass();
                $obj->status = "ok";
                $obj->data = $responseDB;
                $obj->message = "data added successfully";
                return $obj;
            } else {
                $obj = new \stdClass();
                $obj->status = "error";
                $obj->message = "error add data";
                return $obj;
            }
        } else {
            $obj = new \stdClass();
            $obj->status = "error";
            $obj->message = "error in insert data";
            return $obj;
        }
//        return $response;
    }
}