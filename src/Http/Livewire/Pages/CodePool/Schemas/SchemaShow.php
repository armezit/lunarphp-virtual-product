<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Livewire\Component;

class SchemaShow extends Component
{
    /**
     * The Product we are currently editing.
     */
    public CodePoolSchema $schema;

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.pages.code-pool.schemas.show')
            ->layout('adminhub::layouts.app', [
                'title' => $this->schema->name,
            ]);
    }
}
