<div>
    <div class="space-y-2">
        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
            <label class="inline-flex items-center space-x-3 rtl:space-x-reverse">
                <span class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300">
                    {{ __('lunarphp-virtual-product::code-pool.product-settings.schema.label') }}
                </span>
                <span class="text-xs text-gray-500">
                    ({{ __('lunarphp-virtual-product::code-pool.product-settings.schema.strapline') }})
                </span>
            </label>
        </div>

        <x-hub::input.group label="" for="schema">
            @include($listField['view'], ['field' => $listField])
        </x-hub::input.group>
    </div>

    <hr class="my-6 mx-auto w-48 h-1 bg-gray-100 rounded border-0 dark:bg-gray-700" />

    <div>
        <x-hub::button
            tag="a"
            href="{{ route('hub.virtual-product.code-pool.import') }}"
            class="space-x-2 rtl:space-x-reverse"
        >
            <x-hub::icon ref="cloud-upload" class="w-4"/>
            <span>
                {{ __('lunarphp-virtual-product::code-pool.product-settings.import_items_btn') }}
            </span>
        </x-hub::button>
    </div>
</div>
