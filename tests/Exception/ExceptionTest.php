<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 10:43 AM
 */

namespace Tests\Exception;

use All\Exception\BadRequestException;
use All\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    /**
     * @expectedException  \All\Exception\NotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Not Found
     */
    public function testNotFound()
    {
        throw new NotFoundException();
    }

    /**
     * @expectedException \All\Exception\BadRequestException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Bad Request
     */
    public function testBadRequest()
    {
        throw new BadRequestException();
    }
}