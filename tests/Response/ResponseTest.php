<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/16
 * Time: 7:06 PM
 */

namespace Tests\Response;

use All\Response\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testObject()
    {
        $resp = Response::getInstance();
        $this->assertTrue($resp instanceof Response);
    }
}