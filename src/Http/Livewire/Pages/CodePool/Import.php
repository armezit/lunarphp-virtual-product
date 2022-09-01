<?php

namespace Armezit\GetCandy\VirtualProduct\Http\Livewire\Pages\CodePool;

use Livewire\Component;

class Import extends Component
{
    public function render()
    {
        return view('getcandy-virtual-product::livewire.pages.code-pool.import')
            ->layout('adminhub::layouts.app', [
                'title' => __('getcandy-virtual-product::code-pool.pages.import.title'),
            ]);
    }
}
