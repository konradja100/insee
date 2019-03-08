<?php

namespace App\Exception;

class UnknownApiServiceException extends \Exception implements AppExceptionInterface
{
    protected $message = 'Unknown service';
}