<?php declare(strict_types=1);

namespace Acelot\Helpers\Tests;

use function Acelot\Helpers\camelcase;
use const Acelot\Helpers\TK_DEFAULTS;
use const Acelot\Helpers\TK_LOWERCASE;
use const Acelot\Helpers\TK_TO_INT;
use const Acelot\Helpers\TK_UPPERCASE;
use function Acelot\Helpers\tokenize;
use function Acelot\Helpers\underscore;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function camelcaseProvider()
    {
        return [
            ['', ''],
            [' ', ''],
            ['  ', ''],
            ['_', ''],
            ['__', ''],
            ['a', 'A'],
            ['a_', 'A'],
            ['_a', 'A'],
            ['A', 'A'],
            ['ab', 'Ab'],
            ['abc', 'Abc'],
            ['a_b', 'AB'],
            ['a_b_c', 'ABC'],
            ['ab_ab', 'AbAb'],
            ['abc_abc', 'AbcAbc'],
            ['abc_abc_abc', 'AbcAbcAbc'],
            ['a__b', 'AB'],
        ];
    }

    /**
     * @dataProvider camelcaseProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testCamelcase($input, $expected)
    {
        $this->assertEquals($expected, camelcase($input));
    }

    public function camelcaseLcFirstProvider()
    {
        return [
            ['', ''],
            [' ', ''],
            ['  ', ''],
            ['_', ''],
            ['__', ''],
            ['a', 'a'],
            ['a_', 'a'],
            ['_a', 'A'],
            ['A', 'a'],
            ['ab', 'ab'],
            ['abc', 'abc'],
            ['a_b', 'aB'],
            ['a_b_c', 'aBC'],
            ['ab_ab', 'abAb'],
            ['abc_abc', 'abcAbc'],
            ['abc_abc_abc', 'abcAbcAbc'],
            ['a__b', 'aB'],
        ];
    }

    /**
     * @dataProvider camelcaseLcFirstProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testCamelcaseLcFirst($input, $expected)
    {
        $this->assertEquals($expected, camelcase($input, true));
    }

    public function underscoreProvider()
    {
        return [
            ['', ''],
            ['A', 'a'],
            ['Ab', 'ab'],
            ['ABC', 'abc'],
            ['AbAb', 'ab_ab'],
            ['AbbA', 'abb_a'],
            ['aB', 'a_b'],
            ['AbcAbcAbc', 'abc_abc_abc'],
        ];
    }

    /**
     * @dataProvider underscoreProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testUnderscore($input, $expected)
    {
        $this->assertEquals($expected, underscore($input));
    }

    public function tokenizeProvider()
    {
        return [
            'empty' => [
                [
                    ''
                ],
                []
            ],
            'dot joined' => [
                [
                    'a.b.c',
                    '.'
                ],
                ['a', 'b', 'c']
            ],
            'comma joined' => [
                [
                    'a,b,c'
                ],
                ['a', 'b', 'c']
            ],
            'default options' => [
                [
                    ' a, b , '
                ],
                ['a', 'b']
            ],
            'lowercase option' => [
                [
                    'A,b,C,D,Lol',
                    ',',
                    TK_LOWERCASE
                ],
                ['a', 'b', 'c', 'd', 'lol']
            ],
            'uppercase option' => [
                [
                    'A,b,C,D,Lol',
                    ',',
                    TK_UPPERCASE
                ],
                ['A', 'B', 'C', 'D', 'LOL']
            ],
            'to int option' => [
                [
                    '1,2,3,a',
                    ',',
                    TK_TO_INT
                ],
                [1, 2, 3, 0]
            ],
            'defaults and to int option' => [
                [
                    '1,2,3,a',
                    ',',
                    TK_DEFAULTS | TK_TO_INT
                ],
                [1, 2, 3]
            ],
        ];
    }

    /**
     * @dataProvider tokenizeProvider
     *
     * @param string $input
     * @param array $expected
     */
    public function testTokenize($input, $expected)
    {
        $this->assertEquals($expected, tokenize(...$input));
    }
}
