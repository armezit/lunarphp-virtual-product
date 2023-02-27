<?php

namespace Armezit\Lunar\VirtualProduct\Utils;

use Generator;
use Iterator;

/**
 * ChunkIterator is simple class, using built-in Iterator php class.
 * The use cases of this generator class is to avoid memory limit, that
 * requires a considerable amount of processing time to generate.
 * Instead of executing directly in-memory, we yield the results as
 * many times as we need.
 */
class ChunkIterator
{
    public function __construct(
        protected Iterator $iterator,
        protected int $chunkSize
    ) {
    }

    /**
     * Chunk the given data
     */
    public function get(): Generator
    {
        $chunk = [];

        for ($i = 0; $this->iterator->valid(); $i++) {
            // store the current record into the $chunk array
            $chunk[] = $this->iterator->current();

            // move on to the next record
            $this->iterator->next();

            // if the number of element on the $chunk variable
            // met the chunk size, we yield the result and start
            // over, to the next elements
            if (count($chunk) == $this->chunkSize) {
                yield $chunk;
                $chunk = [];
            }
        }

        // if the chunk size is positive, we yield the results
        if (count($chunk) > 0) {
            yield $chunk;
        }
    }
}
