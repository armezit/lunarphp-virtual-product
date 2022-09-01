<div class="space-y-4">
    <div class="grid md:grid-cols-2 gap-4">
        <!-- product selection -->
        <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md shadow sm:p-6">
            <header>
                <h3 class="text-lg font-medium leading-10 text-gray-900">
                    {{ __('getcandy-virtual-product::code-pool.import.section.filters') }}
                </h3>
            </header>

            <x-hub::input.group label="{{ __('getcandy-virtual-product::code-pool.import.input.product') }}"
                                for="productId">
                <x-hub::input.select wire:model="productId"
                                     wire:change="onProductChange">
                    <option value readonly>
                        {{ __('adminhub::fieldtypes.dropdown.empty_selection') }}
                    </option>
                    @foreach($this->products as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </x-hub::input.select>
            </x-hub::input.group>

            <x-hub::input.group label="{{ __('getcandy-virtual-product::code-pool.import.input.product_variant') }}"
                                for="productVariantId">
                <x-hub::input.select wire:model="productVariantId"
                                     wire:change="refresh"
                                     :disabled="blank($productId)">
                    <option value readonly>
                        {{ __('adminhub::fieldtypes.dropdown.empty_selection') }}
                    </option>
                    @foreach($this->productVariants as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </x-hub::input.select>
            </x-hub::input.group>
        </div>

        <!-- batch details -->
        <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md shadow sm:p-6">
            <header>
                <h3 class="text-lg font-medium leading-10 text-gray-900">
                    {{ __('getcandy-virtual-product::code-pool.import.section.batch_details') }}
                </h3>
            </header>

            <x-hub::input.group
                label="{{ __('getcandy-virtual-product::code-pool.import.input.entry_price') }}"
                for="batch.entry_price">
                <div class="mt-1 relative rounded-md shadow-sm">
                    <x-hub::input.text wire:model="batch.entry_price" />
                    <div class="absolute inset-y-0 right-0 flex items-center rtl:left-0 rtl:right-auto">
                        <label for="currency" class="sr-only">Currency</label>
                        <x-hub::input.select
                            wire:model="batch.entry_price_currency"
                            class="form-select block w-full py-2 ps-3 pe-10 text-base bg-transparent border-transparent text-gray-500 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($this->currencies as $k => $v)
                                <option value="{{ $k }}" {{ $defaultCurrencyId === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </x-hub::input.select>
                    </div>
                </div>
            </x-hub::input.group>

            <x-hub::input.group label="{{ __('getcandy-virtual-product::code-pool.import.input.notes') }}"
                                for="batch.notes">
                <x-hub::input.textarea wire:model="batch.notes" rows="1" />
            </x-hub::input.group>
        </div>
    </div>
</div>
