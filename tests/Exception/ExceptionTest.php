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
     * @expectException  \All\Exception\NotFoundException
     * @expectExceptionCode 404
     * @expectExceptionMessage Not Found
     */
    public function testNotFound()
    {
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Not Found');
        throw new NotFoundException();
    }

    /**
     * @expectException \All\Exception\BadRequestException
     * @expectExceptionCode 400
     * @expectExceptionMessage Bad Request
     */
    public function testBadRequest()
    {
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');
        throw new BadRequestException();
    }
}
