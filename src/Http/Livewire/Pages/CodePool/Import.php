<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool;

use Livewire\Component;

class Import extends Component
{
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.pages.code-pool.import')
            ->layout('adminhub::layouts.app', [
                'title' => __('lunarphp-virtual-product::code-pool.pages.import.title'),
            ]);
    }
}
