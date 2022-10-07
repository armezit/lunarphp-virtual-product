<?php

namespace Armezit\Lunar\VirtualProduct\Values;

use Armezit\Lunar\VirtualProduct\Contracts\SourceProvider;
use Livewire\Wireable;
use Spatie\DataTransferObject\DataTransferObject;

class Source extends DataTransferObject implements Wireable
{
    public string $class;

    public bool $enabled;

    public string $name;

    public ?int $stock;

    public array $data;

    /**
     * @var SourceProvider
     */
    private SourceProvider $sourceProvider;

    /**
     * @param  string  $class
     * @param  bool  $enabled
     *
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function __construct(string $class, bool $enabled = false, array $data = [])
    {
        $this->sourceProvider = app($class);

        parent::__construct(
            class: $class,
            enabled: $enabled,
            name: $this->sourceProvider->getName(),
            stock: $this->sourceProvider->getStock(),
            data: $data,
        );
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->sourceProvider->{$name}($arguments);
    }

    public function toLivewire()
    {
        return [
            'class' => $this->class,
            'enabled' => $this->enabled,
            'data' => $this->data,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static(
            $value['class'],
            $value['enabled'],
            $value['data'],
        );
    }
}
