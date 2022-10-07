<div class="shadow sm:rounded-md">
    <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md sm:p-6">
        <header class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    {{ __('lunarphp-virtual-product::slots.virtual-product.heading') }}
                </h3>
                <p class="text-xs text-gray-500">{{ __('lunarphp-virtual-product::slots.virtual-product.strapline') }}</p>
            </div>
            <div>
                <x-hub::input.toggle wire:model="enabled" />
            </div>
        </header>
        @if($enabled)
        <div wire:sort
             sort.options='{source: "source", method: "sortSources"}'
             class="space-y-2">
            @forelse($sources as $index => $source)
                <div wire:key="source_{{ $source->name }}"
                     x-data="{ expanded: {{ $source->getProductSettingsComponent() ? 'true' : 'false' }} }"
                     sort.item="sources"
                     sort.id="{{ $source->class }}">
                    <div class="flex items-center">
                        <div wire:loading
                             wire:target="sort">
                            <x-hub::icon ref="refresh"
                                         style="solid"
                                         class="w-5 mr-2 text-gray-300 rotate-180 animate-spin" />
                        </div>

                        <div wire:loading.remove
                             wire:target="sort">
                            <div sort.handle
                                 class="cursor-grab">
                                <x-hub::icon ref="selector"
                                             style="solid"
                                             class="mr-2 text-gray-400 hover:text-gray-700" />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between w-full p-3 text-sm bg-white border border-transparent rounded shadow-sm sort-item-element hover:border-gray-300">
                            <div class="flex items-center justify-between gap-4 expand">
                                <div>
                                    <input type="hidden" value="{{ $source->class }}" wire:model='sources.{{ $index }}.class'/>
                                    <x-hub::tooltip text="{{ __('lunarphp-virtual-product::default.source.enabled_tooltip') }}">
                                        <x-hub::input.toggle wire:model="sources.{{ $index }}.enabled" value="1" />
                                    </x-hub::tooltip>
                                </div>
                                <span class="font-bold text-gray-700">{{ $source->name }}</span>
                            </div>
                            <div class="flex">
                                @if($source->stock)
                                <label class="inline-flex items-center cursor-pointer">
                                    <span class="block me-2 text-xs font-bold text-gray-500">
                                        {{ __('lunarphp-virtual-product::default.source.stock') }}: {{ $source->stock }}
                                    </span>
                                </label>
                                @endif
                                @if ($source->getProductSettingsComponent())
                                    <button @click="expanded = !expanded">
                                        <div class="transition-transform"
                                             :class="{
                                                         '-rotate-90 ': expanded
                                                     }">
                                            <x-hub::icon ref="chevron-left"
                                                         style="solid" />
                                        </div>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="py-4 pl-2 pr-4 mt-2 ml-8 space-y-2 rounded rtl:pl-4 rtl:pr-2 rtl:ml-0 rtl:mr-8"
                         x-show="expanded">
                        @if ($source->getProductSettingsComponent())
                            @livewire($source->getProductSettingsComponent(), [
                                'product' => $this->slotModel,
                            ])
                        @endif
                    </div>
                </div>
            @empty
                <div class="w-full text-center text-gray-500">
                    {{ __('lunarphp-virtual-product::slots.virtual-product.no_sources') }}
                </div>
            @endforelse
        </div>
        @endif
    </div>
</div>
