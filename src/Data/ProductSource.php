<?php

namespace Armezit\Lunar\VirtualProduct\Data;

use Armezit\Lunar\VirtualProduct\Contracts\SourceProvider;
use Livewire\Wireable;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductSource extends Data implements Wireable
{
    use WireableData;

    public string $name;

    public ?int $stock;

    public ?string $productSettingsComponent;

    /**
     * @var SourceProvider
     */
    private SourceProvider $sourceProvider;

    /**
     * @param string $class
     * @param bool $enabled
     * @param array $meta
     */
    public function __construct(
        public string $class,
        public bool   $enabled = false,
        public array  $meta = [],
    )
    {
        $this->sourceProvider = app($class);
        $this->name = $this->sourceProvider->getName();
        $this->stock = $this->sourceProvider->getStock();
        $this->productSettingsComponent = $this->sourceProvider->getProductSettingsComponent();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->sourceProvider->{$name}($arguments);
    }
}
