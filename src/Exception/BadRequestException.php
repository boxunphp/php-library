<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:03 AM
 */

namespace All\Exception;

use All\Utils\HttpCode;

class BadRequestException extends Exception
{
    protected $code = HttpCode::BAD_REQUEST;
    protected $message = 'Bad Request';
}