<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 10:38 AM
 */

namespace All\Exception;

class NotFoundException extends Exception
{
    protected $code = 404;
    protected $message = 'Not Found';
}