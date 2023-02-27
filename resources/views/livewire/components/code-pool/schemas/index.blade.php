<div class="flex-col space-y-4">
    <header class="sm:flex sm:justify-between sm:items-center">
        <h1 class="text-xl font-bold text-gray-900 md:text-2xl dark:text-white">
            {{ __('lunarphp-virtual-product::code-pool.pages.schemas.index.title') }}
        </h1>

        <div class="mt-4 sm:mt-0">
            <x-hub::button tag="a"
                           href="{{ route('hub.virtual-products.code-pool.schemas.create') }}">
                {{ __('lunarphp-virtual-product::code-pool.pages.schemas.index.create_btn') }}
            </x-hub::button>
        </div>
    </header>

    @livewire('hub.lunarphp-virtual-product.components.code_pool.schemas.table')
</div>
