<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/17
 * Time: 2:37 PM
 */

namespace All\Utils;

trait ErrorTrait
{
    private $errCode = 0;
    private $errMsg = '';

    public function getErrorCode()
    {
        return $this->errCode;
    }

    public function getErrorMessage()
    {
        return $this->errMsg;
    }

    public function setErrorCode($code)
    {
        $this->errCode = $code;
    }

    public function setErrorMessage($message)
    {
        $this->errMsg = $message;
    }
}