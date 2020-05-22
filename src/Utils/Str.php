<?php

namespace All\Utils;

use All\Instance\InstanceTrait;

class Str
{
    use InstanceTrait;

    public static function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    public function isMobile($phone)
    {
        return preg_match("/^1(3[0-9]|8[0-9]|5[0-9]|7[0135678]|47|66|9[89])\d{8}$/", $phone);
    }

    /**
     * 隐藏手机号码中间位
     */
    public function hidePhone($phone)
    {
        if (empty($phone) || strlen($phone) != 11) {
            return $phone;
        }

        return substr($phone, 0, 3) . '****' . substr($phone, -4);
    }

    /**
     * 去除首尾全角及半角空格,多个空格合并为一个
     */
    public function trim($str)
    {
        $str = preg_replace('/( |　|\r\n|\r|\n)+/', ' ', $str);
        return trim(preg_replace("/^　+|　+$/ ", " ", $str));
    }
}
