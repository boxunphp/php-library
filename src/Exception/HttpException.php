<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:09 AM
 */

namespace All\Exception;

use All\Utils\HttpCode;

class HttpException extends Exception
{
    public function __construct($code = 0)
    {
        parent::__construct(HttpCode::message($code), $code);
    }
}