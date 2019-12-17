<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:05 AM
 */

namespace All\Exception;

use All\Utils\HttpCode;

class ServerErrorException extends Exception
{
    protected $code = HttpCode::INTERNAL_SERVER_ERROR;
    protected $message = 'Server Error';
}