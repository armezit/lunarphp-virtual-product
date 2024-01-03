<div class="flex-col space-y-4">
    <header class="sm:flex sm:justify-between sm:items-center">
        <h1 class="text-xl font-bold md:text-2xl">
            {{ __('lunarphp-virtual-product::code-pool.pages.batches.index.title') }}
        </h1>
    </header>

    @livewire('hub.lunarphp-virtual-product.components.code_pool.batches.table', [
        'hasPagination' => true,
        'canSaveSearches' => false,
    ])
</div>
