<div class="space-y-6">
    <div>
        @livewire('hub.lunarphp-virtual-product.components.code_pool.import')
    </div>
    <div class="p-6 overflow-hidden bg-white shadow sm:rounded-md">
        <h2 class="mb-4 text-normal font-bold md:text-lg">Recent Import Jobs</h2>
        @livewire('hub.lunarphp-virtual-product.components.code_pool.batches.table', [
            'hasPagination' => false,
            'searchable' => false,
            'filterable' => false,
            'canSaveSearches' => false,
            'onlyRecent' => true,
            'poll' => '15s',
        ])
    </div>
</div>
