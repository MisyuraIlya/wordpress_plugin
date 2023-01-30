<?php

namespace App;

class ResponseApi
{
    public function __construct($data, $message)
    {
        $this->data = $data;
        $this->message = $message;
    }

    public function OnSuccess()
    {
        $obj = new \stdClass();
        $obj->status = "success";
        $obj->data = $this->data;
        $obj->message = $this->message;

        echo json_encode($obj);
    }

    public function OnError()
    {
        $obj = new \stdClass();
        $obj->status = "error";
        $obj->data = null;
        $obj->message = $this->message;
        echo json_encode($obj);
    }
}