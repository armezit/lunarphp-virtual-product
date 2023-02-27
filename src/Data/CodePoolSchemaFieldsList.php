<?php

namespace Armezit\Lunar\VirtualProduct\Data;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Traversable;

class CodePoolSchemaFieldsList extends Data implements Wireable, Countable, ArrayAccess, IteratorAggregate
{
    /**
     * @param DataCollection<CodePoolSchemaField> $fields
     */
    public function __construct(
        #[DataCollectionOf(CodePoolSchemaField::class)]
        public DataCollection $fields,
    )
    {
    }

    public function toLivewire()
    {
        return $this->fields->toArray();
    }

    public static function fromLivewire($value): static
    {
        return new static(
            fields: CodePoolSchemaField::collection($value)
        );
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->fields->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): CodePoolSchemaField
    {
        return $this->fields->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->fields->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->fields->offsetUnset($offset);
    }

    public function getIterator(): Traversable
    {
        return $this->fields->getIterator();
    }

    public function count(): int
    {
        return count($this->fields);
    }
}
