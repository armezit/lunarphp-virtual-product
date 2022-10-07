<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Sources\CodePool;
use Lunar\FieldTypes\ListField;
use Lunar\Models\Product;
use Livewire\Component;

class ProductSettings extends Component
{
    /**
     * @var Product|null
     */
    public ?Product $product;

    /**
     * @var array
     */
    public array $schema;

    /**
     * @var array
     */
    private array $listField;

    protected function rules()
    {
        return [
            'schema' => 'required|array|min:1',
            'schema.*' => 'required|string|distinct',
        ];
    }

    public function mount()
    {
        $this->initSchema();
        self::initListField();
    }

    public function hydrate()
    {
        self::initListField();
    }

    private function initSchema()
    {
        if ($this->product === null || ! $this->product->exists) {
            $this->schema = [];

            return;
        }

        $this->schema = CodePoolSchema::query()
            ->where(['product_id' => $this->product->id])
            ->select('schema')
            ->get()
            ->pluck('schema')
            ->toArray();
    }

    private function initListField()
    {
        $this->listField = [
            'id' => 'schema',
            'signature' => 'schema',
            'type' => ListField::class,
            'view' => app()->make(ListField::class)->getView(),
        ];
    }

    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.product-settings', [
            'listField' => $this->listField,
        ]);
    }

    public function updated(string $prop, mixed $data)
    {
        $this->emitTo('hub.lunarphp-virtual-product.slots.virtual-product-slot', 'sourceUpdated', [
            'source' => CodePool::class,
            'data' => $data,
        ]);
    }
}
