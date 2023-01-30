<?php

namespace App\ERP;

use App\ERP\SAP\SapConnection;
use App\ERP\verifone\VerifoneConnection;
class ErpRouter
{

    public function GetConnection($data)
    {
        switch ($data->erp){
            case 'SAP':
                $con = new SapConnection($data->domain,$data->company_db,$data->username,$data->password);
                break;
            case 'Priority':
                $con = 'priority will add soon';
                break;
            case 'verifone':
                $con = new VerifoneConnection($data->domain,$data->company_db,$data->username,$data->password);
                break;
        }

        return $con;
    }
    
}