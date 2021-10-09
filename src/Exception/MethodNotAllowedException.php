<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:07 AM
 */

namespace All\Exception;

class MethodNotAllowedException extends Exception
{
    protected $code = 405;
    protected $message = 'Method Not Allowed';
}