<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/16
 * Time: 6:58 PM
 */

namespace Tests\Utils;

use All\Utils\HttpCode;
use PHPUnit\Framework\TestCase;

class HttpCodeTest extends TestCase
{
    public function testMessage()
    {
        $this->assertEquals('No Content', HttpCode::message(HttpCode::NO_CONTENT));
    }
}