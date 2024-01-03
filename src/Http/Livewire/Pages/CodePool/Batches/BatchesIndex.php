<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Batches;

use Livewire\Component;

class BatchesIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.pages.code-pool.batches.index')
            ->layout('adminhub::layouts.app', [
                'title' => __('lunarphp-virtual-product::code-pool.pages.batches.index.title'),
            ]);
    }
}
