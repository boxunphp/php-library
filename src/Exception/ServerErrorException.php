<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:05 AM
 */

namespace All\Exception;

class ServerErrorException extends Exception
{
    protected $code = 500;
    protected $message = 'Server Error';
}