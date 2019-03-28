<?php declare(strict_types=1);

namespace Acelot\Helpers;

/**
 * ChunkIterator is an analogue of the `array_chunk` function, but works with any `\Iterator`.
 * @see https://www.php.net/manual/en/class.iterator.php
 */
class ChunkIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @var bool
     */
    private $preserveKeys;

    /**
     * @var int
     */
    private $chunkIndex = 0;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @param \Iterator $iterator     Wrapped iterator
     * @param int       $chunkSize    Maximum number of elements in the chunk buffer (greater than 0)
     * @param bool      $preserveKeys If true, then keep the keys of the wrapped iterator
     */
    public function __construct(\Iterator $iterator, int $chunkSize, bool $preserveKeys = false)
    {
        if ($chunkSize < 1) {
            throw new \InvalidArgumentException('Chunk size must be greater than zero');
        }

        $this->iterator = $iterator;
        $this->chunkSize = $chunkSize;
        $this->preserveKeys = $preserveKeys;
    }

    public function current()
    {
        return $this->buffer;
    }

    public function next()
    {
        $this->fillChunkBuffer();
        $this->chunkIndex++;
    }

    public function key()
    {
        return $this->chunkIndex;
    }

    public function valid()
    {
        if ($this->chunkIndex === 0) {
            $this->fillChunkBuffer();
        }
        return !empty($this->buffer);
    }

    public function rewind()
    {
        $this->iterator->rewind();
        $this->chunkIndex = 0;
        $this->buffer = [];
    }

    private function fillChunkBuffer()
    {
        $this->buffer = [];
        $i = 0;
        while ($this->iterator->valid() && $i++ < $this->chunkSize) {
            if ($this->preserveKeys) {
                $this->buffer[$this->iterator->key()] = $this->iterator->current();
            } else {
                $this->buffer[] = $this->iterator->current();
            }
            $this->iterator->next();
        }
    }
}
