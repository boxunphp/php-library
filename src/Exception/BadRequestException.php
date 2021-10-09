<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:03 AM
 */

namespace All\Exception;

class BadRequestException extends Exception
{
    protected $code = 400;
    protected $message = 'Bad Request';
}