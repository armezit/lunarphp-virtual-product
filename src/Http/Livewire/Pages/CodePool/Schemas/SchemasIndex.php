<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Schemas;

use Livewire\Component;

class SchemasIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.pages.code-pool.schemas.index')
            ->layout('adminhub::layouts.app', [
                'title' => __('lunarphp-virtual-product::code-pool.pages.schemas.index.title'),
            ]);
    }
}
