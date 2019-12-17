<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:06 AM
 */

namespace All\Exception;

use All\Utils\HttpCode;

class ForbiddenException extends Exception
{
    protected $code = HttpCode::FORBIDDEN;
    protected $message = 'Forbidden';
}