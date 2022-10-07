<?php

namespace Armezit\Lunar\VirtualProduct\Values;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Livewire\Wireable;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Traversable;

class ProductSources extends DataTransferObject implements Wireable, ArrayAccess, IteratorAggregate
{
    /**
     * @var Source[]
     */
    #[CastWith(ArrayCaster::class, itemType: Source::class)]
    public array $sources;

    public function toLivewire()
    {
        return collect($this->sources)
            ->map(fn (Source $source) => $source->toLivewire())
            ->toArray();
    }

    public static function fromLivewire($value)
    {
        return new static(
            sources: collect($value)
                ->map(fn (array $args) => new Source($args['class'], $args['enabled'], $args['data']))
                ->toArray(),
        );
    }

    public function setSourceData(string $class, array $data)
    {
        foreach ($this->sources as $source) {
            if ($source->class === $class) {
                $source->data = $data;
                break;
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->sources[$offset]);
    }

    public function offsetGet(mixed $offset): Source
    {
        return $this->sources[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->sources[] = $value;
        } else {
            $this->sources[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->sources[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->sources);
    }
}
