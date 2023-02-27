<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Livewire\Component;
use Livewire\WithPagination;

class SchemasIndex extends Component
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
    public function getSchemasProperty()
    {
        return CodePoolSchema::search($this->search)->paginate(50);
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.schemas.index')
            ->layout('adminhub::layouts.base');
    }
}
