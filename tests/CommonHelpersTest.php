<?php declare(strict_types=1);

namespace Acelot\Helpers\Tests;

use function Acelot\Helpers\get_by_path;
use function Acelot\Helpers\req_by_path;
use PHPUnit\Framework\TestCase;

class CommonHelpersTest extends TestCase
{
    public function reqByPathProvider()
    {
        return [
            [
                [],
                '',
                []
            ],
            [
                ['я' => 1],
                "['я']",
                1
            ],
            [
                ['a' => 1, 2, 3],
                "[#first]",
                1
            ],
            [
                ['a' => 1, 2, 3],
                "[#last]",
                3
            ],
            [
                ['a' => ['b' => 2]],
                "['a']['b']",
                2
            ],
            [
                ['a' => 1, 2, 3],
                "[0]",
                2
            ],
            [
                [1, 2, 3, [10, 20, 30]],
                "[3][1]",
                20
            ],
            [
                [1, 2, 3, [10, 20, 30]],
                "[3][#last]",
                30
            ],
            [
                (object)['a' => 1],
                "->a",
                1
            ],
            [
                (object)['a' => (object)['b' => 2]],
                "->a->b",
                2
            ],
            [
                (object)['a' => [1, 2, 3]],
                "->a[0]",
                1
            ],
            [
                (object)['a' => [1, 2, 3]],
                "->a[#last]",
                3
            ],
            [
                (object)['a' => ['b' => [1, 2]]],
                "->a['b'][#first]",
                1
            ],
            [
                (object)['#first' => [1, 2, 3]],
                "->{'#first'}[#first]",
                1
            ],
            [
                ['a' => true, 'b' => (object)['c' => [1, 2, (object)['d' => 'hello']]]],
                "['b']->c[#last]->d",
                'hello'
            ]
        ];
    }

    /**
     * @dataProvider reqByPathProvider
     *
     * @param mixed  $var
     * @param string $path
     * @param bool   $expected
     */
    public function testReqByPath($var, string $path, $expected)
    {
        try {
            $this->assertEquals($expected, req_by_path($var, $path));
        } catch (\OutOfBoundsException $e) {
            $this->fail();
        }
    }

    public function reqByPathInvalidProvider()
    {
        return [
            [
                [],
                "['a']",
                null,
                null
            ],
            [
                [],
                '->a',
                1,
                1
            ],
            [
                ['a' => 1, 2, 3],
                "[2]",
                100,
                100
            ],
            [
                ['a' => ['b' => 2]],
                "['a']->b",
                null,
                null
            ],
            [
                [1, 2, 3, [10, 20, 30]],
                "[3][#last][0]",
                null,
                null
            ],
            [
                (object)['a' => 1],
                "->b",
                null,
                null
            ],
            [
                (object)['a' => (object)['b' => 2]],
                "->a[#first]",
                null,
                null
            ],
            [
                (object)['a' => [1, 2, 3]],
                "['a']",
                null,
                null
            ]
        ];
    }

    /**
     * @dataProvider reqByPathInvalidProvider
     *
     * @param mixed  $var
     * @param string $path
     */
    public function testReqByPathInvalid($var, string $path)
    {
        $this->expectException(\OutOfBoundsException::class);
        req_by_path($var, $path);
    }

    /**
     * @dataProvider reqByPathInvalidProvider
     *
     * @param mixed  $var
     * @param string $path
     */
    public function getByPathInvalid($var, string $path, $default, $expected)
    {
        $this->assertEquals($expected, get_by_path($var, $path, $default));
    }

    public function testInvalidPath()
    {
        $this->expectException(\InvalidArgumentException::class);
        req_by_path(null, 'a');
        req_by_path(null, '1');
        req_by_path(null, '[0]a');
        req_by_path(null, '->a[a]');
        req_by_path(null, '[a]->->');
        req_by_path(null, '[[a]]');
        req_by_path(null, '->%a');
    }
}
