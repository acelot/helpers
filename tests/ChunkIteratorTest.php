<?php declare(strict_types=1);

namespace Acelot\Helpers\Tests;

use Acelot\Helpers\ChunkIterator;
use PHPUnit\Framework\TestCase;

class ChunkIteratorTest extends TestCase
{
    private const DATA = [
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
    ];

    public function iteratorDataProvider()
    {
        return [
            [
                new \ArrayIterator(self::DATA),
                1,
                [
                    ['one'],
                    ['two'],
                    ['three'],
                    ['four'],
                    ['five'],
                    ['six'],
                    ['seven'],
                    ['eight'],
                    ['nine'],
                    ['ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                2,
                [
                    ['one', 'two'],
                    ['three', 'four'],
                    ['five', 'six'],
                    ['seven', 'eight'],
                    ['nine', 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                3,
                [
                    ['one', 'two', 'three'],
                    ['four', 'five', 'six'],
                    ['seven', 'eight', 'nine'],
                    ['ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                4,
                [
                    ['one', 'two', 'three', 'four'],
                    ['five', 'six', 'seven', 'eight'],
                    ['nine', 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                5,
                [
                    ['one', 'two', 'three', 'four', 'five'],
                    ['six', 'seven', 'eight', 'nine', 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                6,
                [
                    ['one', 'two', 'three', 'four', 'five', 'six'],
                    ['seven', 'eight', 'nine', 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                7,
                [
                    ['one', 'two', 'three', 'four', 'five', 'six', 'seven'],
                    ['eight', 'nine', 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                8,
                [
                    ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight'],
                    ['nine', 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                9,
                [
                    ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'],
                    ['ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                10,
                [
                    self::DATA,
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                11,
                [
                    self::DATA,
                ]
            ],
        ];
    }

    public function iteratorWithKeyPreservingDataProvider()
    {
        return [
            [
                new \ArrayIterator(self::DATA),
                1,
                [
                    [0 => 'one'],
                    [1 => 'two'],
                    [2 => 'three'],
                    [3 => 'four'],
                    [4 => 'five'],
                    [5 => 'six'],
                    [6 => 'seven'],
                    [7 => 'eight'],
                    [8 => 'nine'],
                    [9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                2,
                [
                    [0 => 'one', 1 => 'two'],
                    [2 => 'three', 3 => 'four'],
                    [4 => 'five', 5 => 'six'],
                    [6 => 'seven', 7 => 'eight'],
                    [8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                3,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three'],
                    [3 => 'four', 4 => 'five', 5 => 'six'],
                    [6 => 'seven', 7 => 'eight', 8 => 'nine'],
                    [9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                4,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four'],
                    [4 => 'five', 5 => 'six', 6 => 'seven', 7 => 'eight'],
                    [8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                5,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five'],
                    [5 => 'six', 6 => 'seven', 7 => 'eight', 8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                6,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five', 5 => 'six'],
                    [6 => 'seven', 7 => 'eight', 8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                7,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five', 5 => 'six', 6 => 'seven'],
                    [7 => 'eight', 8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                8,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five', 5 => 'six', 6 => 'seven', 7 => 'eight'],
                    [8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                9,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five', 5 => 'six', 6 => 'seven', 7 => 'eight', 8 => 'nine'],
                    [9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                10,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five', 5 => 'six', 6 => 'seven', 7 => 'eight', 8 => 'nine', 9 => 'ten'],
                ]
            ],
            [
                new \ArrayIterator(self::DATA),
                11,
                [
                    [0 => 'one', 1 => 'two', 2 => 'three', 3 => 'four', 4 => 'five', 5 => 'six', 6 => 'seven', 7 => 'eight', 8 => 'nine', 9 => 'ten'],
                ]
            ],
        ];
    }

    public function uniqueIteratorDataProvider()
    {
        return [
            [
                new \ArrayIterator([
                    'one',
                    'one',
                ]),
                2,
                [
                    ['one'],
                ]
            ],
            [
                new \ArrayIterator([
                    'one',
                    'two',
                    'one',
                ]),
                2,
                [
                    ['one', 'two'],
                    ['one'],
                ]
            ],
            [
                new \ArrayIterator([
                    'one',
                    'two',
                    'one',
                    'two',
                ]),
                3,
                [
                    ['one', 'two'],
                    ['two'],
                ]
            ],
            [
                new \ArrayIterator([
                    'one',
                    'two',
                    'one',
                    'two',
                ]),
                4,
                [
                    ['one', 'two'],
                ]
            ],
            [
                new \ArrayIterator([
                    'one',
                    'one',
                    'one',
                    'one',
                    'one',
                    'one',
                    'two',
                ]),
                6,
                [
                    ['one'],
                    ['two'],
                ]
            ],
            [
                new \ArrayIterator([
                    'one',
                    'one',
                    'one',
                    'one',
                    'one',
                    'two',
                ]),
                2,
                [
                    ['one'],
                    ['one'],
                    ['one', 'two'],
                ]
            ],
        ];
    }

    /**
     * @dataProvider iteratorDataProvider
     */
    public function testIterator($iterator, $chunkSize, $expected)
    {
        $chunks = iterator_to_array(new ChunkIterator($iterator, $chunkSize));
        $this->assertEquals($expected, $chunks);
    }

    /**
     * @dataProvider iteratorWithKeyPreservingDataProvider
     */
    public function testIteratorWithKeyPreserving($iterator, $chunkSize, $expected)
    {
        $chunks = iterator_to_array(new ChunkIterator($iterator, $chunkSize, true));
        $this->assertEquals($expected, $chunks);
    }

    /**
     * @dataProvider uniqueIteratorDataProvider
     */
    public function testUniqueIterator($iterator, $chunkSize, $expected)
    {
        $chunks = iterator_to_array(new ChunkIterator($iterator, $chunkSize, false, true));
        $this->assertEquals($expected, $chunks);
    }
}
