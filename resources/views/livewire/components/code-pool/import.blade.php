<form action="#"
      method="POST"
      wire:submit.prevent="import">
    <div class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
            <!-- product selection -->
            <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md shadow sm:p-6">
                <header>
                    <h3 class="text-lg font-medium leading-10 text-gray-900">
                        {{ __('lunarphp-virtual-product::code-pool.import.section.filters') }}
                    </h3>
                </header>

                <x-hub::input.group label="{{ __('lunarphp-virtual-product::code-pool.import.input.product') }}"
                                    for="productId">
                    <x-hub::input.select wire:model="productId">
                        <option value readonly>
                            {{ __('adminhub::fieldtypes.dropdown.empty_selection') }}
                        </option>
                        @foreach($this->products as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </x-hub::input.select>
                </x-hub::input.group>

                <x-hub::input.group label="{{ __('lunarphp-virtual-product::code-pool.import.input.product_variant') }}"
                                    for="batch.purchasable_id">
                    <x-hub::input.select wire:model="batch.purchasable_id" :disabled="blank($productId)">
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
                        {{ __('lunarphp-virtual-product::code-pool.import.section.batch_details') }}
                    </h3>
                </header>

                <x-hub::input.group
                    label="{{ __('lunarphp-virtual-product::code-pool.import.input.entry_price') }}"
                    for="batch.entry_price">
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <x-hub::input.text wire:model="batch.entry_price" />
                        <div class="absolute inset-y-0 right-0 flex items-center rtl:left-0 rtl:right-auto">
                            <label for="currency" class="sr-only">Currency</label>
                            <x-hub::input.select
                                wire:model="batch.entry_price_currency_id"
                                class="form-select block w-full py-2 ps-3 pe-10 text-base bg-transparent border-transparent text-gray-500 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach($this->currencies as $k => $v)
                                    <option value="{{ $k }}" {{ $defaultCurrencyId === $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </x-hub::input.select>
                        </div>
                    </div>
                </x-hub::input.group>

                <x-hub::input.group label="{{ __('lunarphp-virtual-product::code-pool.import.input.notes') }}"
                                    for="batch.notes">
                    <x-hub::input.textarea wire:model="batch.notes" rows="1" />
                </x-hub::input.group>
            </div>
        </div>

        <div
            class="grid md:grid-cols-2 gap-4"
            x-data="{ open: @entangle('showCsvImporter') }"
            x-show="open"
            x-cloak
        >
            <!-- uploader -->
            <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md shadow sm:p-6">
                <header class="space-y-1">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        {{ __('lunarphp-virtual-product::code-pool.import.section.file_upload') }}
                    </h3>
                    <p class="text-xs text-gray-500">{{ __('lunarphp-virtual-product::code-pool.import.section.strapline') }}</p>
                </header>

                <div class="flex flex-1 flex-col justify-between">
                    <div x-data="{ hide: @entangle('file') }" x-show="!hide">
                        <x-hub::input.fileupload wire:model="file" :filetypes="$allowedFiletypes" />
                    </div>

                    @if ($file)
                        <div class="flex p-4 mb-4">
                            <div class="ml-3 font-medium text-green-600">
                                <x-hub::icon ref="table-cells" class="w-4 h-4" />
                                {{ $file->getClientOriginalName() }}
                            </div>
                            <button
                                type="button"
                                class="inline-flex justify-center w-6 p-1.5 rounded-full text-gray-500 focus:ring-2"
                                aria-label="Remove"
                                wire:click.prevent="removeFile()"
                            >
                                <span class="sr-only">Remove file</span>
                                <x-hub::icon ref="x-circle" class="w-full h-full" />
                            </button>
                        </div>
                    @endif

                    @error('file')
                    <span class="mt-2 text-red-500 font-medium text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- column selection -->
            <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md shadow sm:p-6">
                <header class="flex justify-between">
                    <div class="space-y-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Match columns</h3>
                    </div>
                </header>

                @if ($fileHeaders)
                <div class="mt-8 space-y-5">
                    @foreach ($columnsToMap as $column => $value)
                    <div class="grid grid-cols-4 gap-4 items-start">
                        <label for="{{ $column }}" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2 col-span-1">
                            {{ $columnLabels[$column] ?? ucfirst(str_replace(['_', '-'], ' ', $column)) }}
                        </label>
                        <div class="mt-1 sm:mt-0 sm:col-span-3">
                            <x-hub::input.select wire:model.defer="columnsToMap.{{$column}}" name="{{ $column }}" id="{{ $column }}">
                                <option value="">{{ __('Select a column') }}</option>
                                @foreach ($fileHeaders as $fileHeader)
                                    <option value="{{$fileHeader}}">{{ $fileHeader }}</option>
                                @endforeach
                            </x-hub::input.select>

                            @error('columnsToMap.' . $column)
                            <span class="mt-2 text-red-500 font-medium text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <div class="py-2 text-gray-400 text-sm">Upload a CSV file to start column matching.</div>
                @endif

            </div>
        </div>

        <div class="px-4 py-3 text-right bg-gray-50 sm:px-6">
            <x-hub::button type="submit"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :disabled="!$this->canImport">
                {{ __('lunarphp-virtual-product::code-pool.import.submit_btn') }}
            </x-hub::button>
        </div>
    </div>
</form>
