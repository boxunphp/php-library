<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:06 AM
 */

namespace All\Exception;

class ForbiddenException extends Exception
{
    protected $code = 403;
    protected $message = 'Forbidden';
}