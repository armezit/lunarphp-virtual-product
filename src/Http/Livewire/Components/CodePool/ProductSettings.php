<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\SourceProviders\CodePool;
use Livewire\Component;
use Lunar\Models\Product;

class ProductSettings extends Component
{
    public Product $product;

    public ?string $schemaId;

    protected function rules()
    {
        return [
            'schema' => 'required',
        ];
    }

    public function mount()
    {
        $this->initSchema();
    }

    private function initSchema()
    {
        $virtualProduct = VirtualProduct::onlyCodePool()
            ->where(['product_id' => $this->product->id])
            ->first();
        $this->schemaId = $virtualProduct?->meta['schemaId'];
    }

    public function getSchemasProperty()
    {
        return CodePoolSchema::get();
    }

    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.product-settings');
    }

    public function updated(string $prop, mixed $data)
    {
        $this->emitTo('hub.lunarphp-virtual-product.slots.virtual-product-slot', 'sourceUpdated', [
            'source' => CodePool::class,
            'data' => [$prop => $data],
        ]);
    }
}
