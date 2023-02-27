<?php

namespace Armezit\Lunar\VirtualProduct\Data;

use ArrayAccess;
use IteratorAggregate;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Traversable;

class ProductSourcesList extends Data implements Wireable, ArrayAccess, IteratorAggregate
{
    /**
     * @param  DataCollection<ProductSource>  $sources
     */
    final public function __construct(
        #[DataCollectionOf(ProductSource::class)]
        public DataCollection $sources,
    ) {
    }

    public function sourceMeta(string $class, array $meta)
    {
        $source = $this->sources->sole(fn (ProductSource $s) => $s->class === $class);
        $source->meta = $meta;
    }

    public function toLivewire()
    {
        return $this->sources->toArray();
    }

    public static function fromLivewire($value): static
    {
        return new static(
            sources: ProductSource::collection(
                collect($value)
                    ->map(fn (array $source) => new ProductSource(
                        class: $source['class'],
                        enabled: $source['enabled'],
                        meta: $source['meta']
                    ))
                    ->all()
            )
        );
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->sources->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->sources->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->sources->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->sources->offsetUnset($offset);
    }

    public function getIterator(): Traversable
    {
        return $this->sources->getIterator();
    }
}
