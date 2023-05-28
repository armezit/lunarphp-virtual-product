<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;

class SchemaCreate extends AbstractSchema
{
    /**
     * Called when we mount the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->schema = new CodePoolSchema();
        $this->fields = CodePoolSchemaField::collection([]);
        $this->addField();
    }

    /**
     * Validates the LiveWire request, updates the model and dispatches and event.
     *
     * @return void
     */
    public function create()
    {
        $this->validate();

        $this->schema->fields = collect($this->fields);
        $this->schema->save();

        $this->notify(
            'Code pool schema successfully created.',
            'hub.virtual-products.code-pool.schemas.index'
        );
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.schemas.create')
            ->layout('adminhub::layouts.base');
    }
}
