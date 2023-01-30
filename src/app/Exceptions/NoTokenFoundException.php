<?php

namespace App\Exceptions;

class NoTokenFoundException extends \Exception
{
    protected $message = 'No token Found';
}