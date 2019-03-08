<?php

namespace App\Exception;

class NotFoundException extends \Exception implements AppExceptionInterface
{
    protected $message = 'Not found';
}