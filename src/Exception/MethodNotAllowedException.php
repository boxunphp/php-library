<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:07 AM
 */

namespace All\Exception;

use All\Utils\HttpCode;

class MethodNotAllowedException extends Exception
{
    protected $code = HttpCode::METHOD_NOT_ALLOWED;
    protected $message = 'Method Not Allowed';
}