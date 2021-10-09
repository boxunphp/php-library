<?php
namespace Tests\Router;

use PHPUnit\Framework\TestCase;
use All\Router\Parser;

/**
 * @group ParserTest
 */
class ParserTest extends TestCase
{
    /**
     * @dataProvider provideTestParse
     *
     * @param array<string|string[]> $expectedRouteDatas
     */
    public function testParse(string $routeString, array $expectedRouteDatas): void
    {
        $parser = new Parser();
        $routeDatas = $parser->parse($routeString);
        self::assertSame($expectedRouteDatas, $routeDatas);
    }

    /**
     * @dataProvider provideTestParseError
     */
    public function testParseError(string $routeString, string $expectedExceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $parser = new Parser();
        $parser->parse($routeString);
    }

    /**
     * @return mixed[]
     */
    public function provideTestParse(): array
    {
        return [
            [
                '/test',
                [
                    ['/test'],
                ],
            ],
            [
                '/test/{param}',
                [
                    ['/test/', ['param', '[^/]+']],
                ],
            ],
            [
                '/te{ param }st',
                [
                    ['/te', ['param', '[^/]+'], 'st'],
                ],
            ],
            [
                '/test/{param1}/test2/{param2}',
                [
                    ['/test/', ['param1', '[^/]+'], '/test2/', ['param2', '[^/]+']],
                ],
            ],
            [
                '/test/{param:\d+}',
                [
                    ['/test/', ['param', '\d+']],
                ],
            ],
            [
                '/test/{param:number}',
                [
                    ['/test/', ['param', '[0-9]+']],
                ],
            ],
            [
                '/test/{ param : \d{1,9} }',
                [
                    ['/test/', ['param', '\d{1,9}']],
                ],
            ],
            [
                '/test[opt]',
                [
                    ['/test'],
                    ['/testopt'],
                ],
            ],
            [
                '/test[/{param}]',
                [
                    ['/test'],
                    ['/test/', ['param', '[^/]+']],
                ],
            ],
            [
                '/{param}[opt]',
                [
                    ['/', ['param', '[^/]+']],
                    ['/', ['param', '[^/]+'], 'opt'],
                ],
            ],
            [
                '/test[/{name}[/{id:[0-9]+}]]',
                [
                    ['/test'],
                    ['/test/', ['name', '[^/]+']],
                    ['/test/', ['name', '[^/]+'], '/', ['id', '[0-9]+']],
                ],
            ],
            [
                '',
                [
                    [''],
                ],
            ],
            [
                '[test]',
                [
                    [''],
                    ['test'],
                ],
            ],
            [
                '/{foo-bar}',
                [
                    ['/', ['foo-bar', '[^/]+']],
                ],
            ],
            [
                '/{_foo:.*}',
                [
                    ['/', ['_foo', '.*']],
                ],
            ],
        ];
    }

    /**
     * @return string[][]
     */
    public function provideTestParseError(): array
    {
        return [
            [
                '/test[opt',
                "Number of opening '[' and closing ']' does not match",
            ],
            [
                '/test[opt[opt2]',
                "Number of opening '[' and closing ']' does not match",
            ],
            [
                '/testopt]',
                "Number of opening '[' and closing ']' does not match",
            ],
            [
                '/test[]',
                'Empty optional part',
            ],
            [
                '/test[[opt]]',
                'Empty optional part',
            ],
            [
                '[[test]]',
                'Empty optional part',
            ],
            [
                '/test[/opt]/required',
                'Optional segments can only occur at the end of a route',
            ],
        ];
    }
}