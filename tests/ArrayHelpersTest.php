<?php declare(strict_types=1);

namespace Acelot\Helpers\Tests;

use function Acelot\Helpers\array_find;
use function Acelot\Helpers\array_find_index;
use function Acelot\Helpers\array_first;
use function Acelot\Helpers\array_get;
use function Acelot\Helpers\array_has;
use function Acelot\Helpers\array_req;
use function Acelot\Helpers\array_to_object;
use function Acelot\Helpers\is_array_flat;
use function Acelot\Helpers\is_array_scalar;
use Acelot\Helpers\Tests\Fixtures\Collection;
use PHPUnit\Framework\TestCase;

class ArrayHelpersTest extends TestCase
{
    public function isArrayFlatProvider()
    {
        return [
            'not sequentially indexed' => [
                [0 => 'a', 2 => 'b'],
                false
            ],
            'associative' => [
                ['a' => 'a', 'b' => 'b'],
                false
            ],
            'mixed indexed' => [
                [1, 2, 'a' => 'a'],
                false
            ],
            'empty' => [
                [],
                true
            ],
            'without keys' => [
                [1, 2, 3, 'a', 'b', 'c'],
                true
            ],
            'sequentially indexed' => [
                [0 => 'a', 1 => 'b', 2 => 'c'],
                true
            ],
        ];
    }

    /**
     * @dataProvider isArrayFlatProvider
     *
     * @param mixed $input
     * @param bool  $expected
     */
    public function testIsArrayFlat($input, $expected)
    {
        $this->assertEquals($expected, is_array_flat($input));
    }

    public function isArrayScalarProvider()
    {
        return [
            'empty' => [
                [],
                true
            ],
            'scalar types' => [
                [1, 1.0, 'a', true],
                true
            ],
            'array' => [
                [[1, 2]],
                false
            ],
            'null' => [
                [null],
                false
            ],
            'object' => [
                [new \stdClass()],
                false
            ],
        ];
    }

    /**
     * @dataProvider isArrayScalarProvider
     *
     * @param mixed $input
     * @param bool  $expected
     */
    public function testIsArrayScalar($input, $expected)
    {
        $this->assertEquals($expected, is_array_scalar($input));
    }

    public function arrayHasProvider()
    {
        return [
            'empty' => [
                [
                    [],
                    'a'
                ],
                false
            ],
            'empty not exists path' => [
                [
                    [1, 2, 3],
                    ''
                ],
                false
            ],
            'empty exists path' => [
                [
                    ['' => 1],
                    ''
                ],
                true
            ],
            'flat array and path isn\'t exists' => [
                [
                    [1, 2, 3],
                    'a'
                ],
                false
            ],
            'flat array and path is exists' => [
                [
                    [1, 2, 3],
                    '1'
                ],
                true
            ],
            'not exists key' => [
                [
                    ['a' => 1, 'b' => 2],
                    'c'
                ],
                false
            ],
            'assoc path' => [
                [
                    ['a' => ['b' => ['c' => 1]]],
                    'a.b.c'
                ],
                true
            ],
            'index path' => [
                [
                    [1, 2, [3, ['a', 'b']]],
                    '2.1.1'
                ],
                true
            ],
            'mixed path' => [
                [
                    ['a' => ['b' => [1, 2, 3]]],
                    'a.b.0'
                ],
                true
            ],
            'mixed path 2' => [
                [
                    ['a', 'b', [1, 2, 3]],
                    '2.2'
                ],
                true
            ],
            'empty keys' => [
                [
                    ['a' => 1, '' => ['' => 2]],
                    '.'
                ],
                true
            ],
        ];
    }

    /**
     * @dataProvider arrayHasProvider
     *
     * @param array $input
     * @param mixed $expected
     */
    public function testArrayHas($input, $expected)
    {
        $this->assertEquals($expected, array_has(...$input));
    }

    public function arrayGetProvider()
    {
        return [
            'empty' => [
                [
                    [],
                    'a'
                ],
                null
            ],
            'empty not exists path' => [
                [
                    [1, 2, 3],
                    ''
                ],
                null
            ],
            'empty exists path' => [
                [
                    ['' => 1],
                    ''
                ],
                1
            ],
            'flat array and path isn\'t exists' => [
                [
                    [1, 2, 3],
                    'a'
                ],
                null
            ],
            'flat array and path is exists' => [
                [
                    [1, 2, 3],
                    '1'
                ],
                2
            ],
            'custom default value' => [
                [
                    ['a' => 1, 'b' => 2],
                    'c',
                    true
                ],
                true
            ],
            'assoc path' => [
                [
                    ['a' => ['b' => ['c' => 1]]],
                    'a.b.c'
                ],
                1
            ],
            'index path' => [
                [
                    [1, 2, [3, ['a', 'b']]],
                    '2.1.1'
                ],
                'b'
            ],
            'mixed path' => [
                [
                    ['a' => ['b' => [1, 2, 3]]],
                    'a.b.0'
                ],
                1
            ],
            'mixed path 2' => [
                [
                    ['a', 'b', [1, 2, 3]],
                    '2.2'
                ],
                3
            ],
            'empty keys' => [
                [
                    ['a' => 1, '' => ['' => 2]],
                    '.'
                ],
                2
            ]
        ];
    }

    /**
     * @dataProvider arrayGetProvider
     *
     * @param array $input
     * @param mixed $expected
     */
    public function testArrayGet($input, $expected)
    {
        $this->assertEquals($expected, array_get(...$input));
    }

    /**
     * @dataProvider arrayGetProvider
     *
     * @param mixed $input
     * @param mixed $expected
     */
    public function testArrayReq($input, $expected)
    {
        try {
            $this->assertEquals($expected, array_req(...array_slice($input, 0, 2)));
        } catch (\Exception $e) {
            $this->assertInstanceOf(\OutOfBoundsException::class, $e);
        }
    }

    public function arrayFirstProvider()
    {
        return [
            'empty array' => [
                [
                    []
                ],
                null
            ],
            'empty array with default value' => [
                [
                    [],
                    4
                ],
                4
            ],
            'array of strings' => [
                [
                    ['a', 'b', 'c']
                ],
                'a'
            ],
            'array of arrays' => [
                [
                    [['a'], ['b'], ['c']]
                ],
                ['a']
            ],
            'associative array' => [
                [
                    ['a' => 1, 'b' => 2, 'c' => [1, 2, 3]]
                ],
                1
            ],
        ];
    }

    /**
     * @dataProvider arrayFirstProvider
     *
     * @param array $input
     * @param mixed $expected
     */
    public function testArrayFirst($input, $expected)
    {
        $this->assertEquals($expected, array_first(...$input));
    }

    public function arrayFindProvider()
    {
        return [
            'empty array' => [
                [
                    [],
                    function ($item) {
                        return $item === true;
                    }
                ],
                null
            ],
            'empty array with default value' => [
                [
                    [],
                    function ($item) {
                        return $item === true;
                    },
                    'default'
                ],
                'default'
            ],
            'array of strings' => [
                [
                    ['a', 'b', 'c'],
                    function ($item) {
                        return $item === 'b';
                    },
                ],
                'b'
            ],
            'complex data' => [
                [
                    [
                        new \DateTime('2018-01-01'),
                        new \DateTime('2018-05-01'),
                        new \DateTime('2018-12-01'),
                    ],
                    function (\DateTime $item) {
                        return $item > new \DateTime('2018-04-01');
                    }
                ],
                new \DateTime('2018-05-01')
            ],
            'associative array' => [
                [
                    ['a' => 1, 'b' => 2, 'c' => [1, 2, 3]],
                    function ($item) {
                        return is_array($item);
                    }
                ],
                [1, 2, 3]
            ]
        ];
    }

    /**
     * @dataProvider arrayFindProvider
     *
     * @param array $input
     * @param mixed $expected
     */
    public function testArrayFind($input, $expected)
    {
        $this->assertEquals($expected, array_find(...$input));
    }

    public function arrayFindIndexProvider()
    {
        return [
            'empty array' => [
                [
                    [],
                    function ($item) {
                        return $item === true;
                    }
                ],
                false
            ],
            'array of strings' => [
                [
                    ['a', 'b', 'c'],
                    function ($item) {
                        return $item === 'b';
                    },
                ],
                1
            ],
            'complex data' => [
                [
                    [
                        new \DateTime('2018-01-01'),
                        new \DateTime('2018-05-01'),
                        new \DateTime('2018-12-01'),
                    ],
                    function (\DateTime $item) {
                        return $item > new \DateTime('2018-04-01');
                    }
                ],
                1
            ],
            'associative array' => [
                [
                    ['a' => 1, 'b' => 2, 'c' => [1, 2, 3]],
                    function ($item) {
                        return is_array($item);
                    }
                ],
                'c'
            ]
        ];
    }

    /**
     * @dataProvider arrayFindIndexProvider
     *
     * @param array $input
     * @param mixed $expected
     */
    public function testArrayFindIndex($input, $expected)
    {
        $this->assertEquals($expected, array_find_index(...$input));
    }

    public function arrayToObjectProvider()
    {
        return [
            'empty array' => [
                []
            ],
            'array of integers' => [
                [1, 2, 3]
            ],
            'array of strings' => [
                ['a', 'b', 'c']
            ],
            'associative array' => [
                ['a' => 1, 'b' => 2, 'c' => [1, 2, 3]]
            ]
        ];
    }

    /**
     * @dataProvider arrayToObjectProvider
     *
     * @param array $input
     */
    public function testArrayToObject($input)
    {
        $this->assertEquals(json_decode(json_encode($input)), array_to_object($input));
    }
}
