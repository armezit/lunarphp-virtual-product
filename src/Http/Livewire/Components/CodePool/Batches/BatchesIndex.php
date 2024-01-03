<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Batches;

use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Livewire\Component;
use Livewire\WithPagination;

class BatchesIndex extends Component
{
    use WithPagination;

    /**
     * The search term.
     *
     * @var string
     */
    public $search = '';

    /**
     * Define what to track in the query string.
     *
     * @var array
     */
    protected $queryString = ['search'];

    public function updatedSearch()
    {
        $this->setPage(1);
    }

    /**
     * Computed method to return customers.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getBatchesProperty()
    {
        return CodePoolBatch::search($this->search)->paginate(50);
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.batches.index')
            ->layout('adminhub::layouts.base');
    }
}
