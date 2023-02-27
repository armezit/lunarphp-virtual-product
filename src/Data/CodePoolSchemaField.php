<?php

namespace Armezit\Lunar\VirtualProduct\Data;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class CodePoolSchemaField extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public ?string           $name,
        #[WithCast(EnumCast::class)]
        public CodePoolFieldType $type,
        public int               $order,
    )
    {
    }
}
