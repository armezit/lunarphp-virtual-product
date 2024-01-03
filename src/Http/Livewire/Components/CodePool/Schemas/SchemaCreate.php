<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;
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
        $this->fields = new CodePoolSchemaFieldsList(
            fields: CodePoolSchemaField::collection([])
        );
        $this->addField();
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
