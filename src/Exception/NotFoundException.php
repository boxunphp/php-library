<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 10:38 AM
 */

namespace All\Exception;

use All\Utils\HttpCode;

class NotFoundException extends Exception
{
    protected $code = HttpCode::NOT_FOUND;
    protected $message = 'Not Found';
}