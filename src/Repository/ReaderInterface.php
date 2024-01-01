<?php

namespace Armezit\Lunar\VirtualProduct\Repository;

use Iterator;

interface ReaderInterface
{
    public function getHeader(): array;

    public function getRecordsCount(): int;

    public function getRecordsIterator(): Iterator;
}
