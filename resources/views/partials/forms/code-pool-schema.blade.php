<div class="overflow-hidden shadow sm:rounded-md">
    <div class="flex-col px-4 py-5 space-y-4 bg-white sm:p-6">
        <x-hub::input.group :label="__('adminhub::inputs.name')"
                            for="name"
                            :error="$errors->first('schema.name')"
                            required>
            <x-hub::input.text wire:model="schema.name"
                               name="name"
                               id="name"
                               :error="$errors->first('schema.name')" />
        </x-hub::input.group>

        <div class="space-y-4">
            <div class="sm:flex sm:justify-between sm:items-center">
                <h1 class="text-lg font-bold text-gray-900 md:text-xl dark:text-white">
                    {{ __('lunarphp-virtual-product::code-pool.partials.forms.schema.data_fields') }}
                </h1>

                <div class="mt-4 sm:mt-0">
                    <x-hub::button
                        wire:click.prevent="addField"
                        size="xs"
                        title="{{ __('lunarphp-virtual-product::code-pool.partials.forms.schema.add_field') }}"
                    >
                        <x-hub::icon ref="plus" class="w-6 h-6"/>
                    </x-hub::button>
                </div>
            </div>

            <div
                class="p-2 space-y-2"
                wire:sort sort.options='{group: "fields", method: "sortFields"}'
            >
                @foreach($this->fields as $index => $field)
                    <div
                        class="flex items-center justify-between w-full p-3 space-x-4 rtl:space-x-reverse text-sm bg-white border border-transparent rounded sort-item-element hover:border-gray-300"
                        wire:key="field_{{ $field->name }}"
                        sort.item="fields"
                        sort.parent="{{ $schema->id }}"
                        sort.id="{{ $field->name }}"
                    >
                        <div sort.handle class="cursor-grab">
                            <x-hub::icon ref="selector" style="solid" class="ltr:mr-2 rtl:ml-2 text-gray-400 hover:text-gray-700" />
                        </div>
                        <div class="grow grid grid-cols-1 gap-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <x-hub::input.text wire:model.defer="fields.{{ $loop->index }}.name" class="truncate grow" />
                                @error("fields.{$loop->index}.name")<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <x-hub::input.select wire:model="fields.{{ $loop->index }}.type">
                                    @foreach($this->fieldTypes as $type => $label)
                                    <option value="{{ $type }}" @if($field->type === $type) selected @endif>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </x-hub::input.select>
                                @error("fields.{$loop->index}.type")<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <x-hub::dropdown minimal>
                                <x-slot name="options">
                                    <x-hub::dropdown.button wire:click="$set('optionValueToDeleteId', {{ $field->name }})"
                                                            class="flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-50">
                                        <span class="text-red-500">{{ __('lunarphp-virtual-product::code-pool.partials.forms.schema.delete_field') }}</span>
                                    </x-hub::dropdown.button>
                                </x-slot>
                            </x-hub::dropdown>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="px-4 py-3 text-right bg-gray-50 sm:px-6">
        <button type="submit"
                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('adminhub::global.save') }}
        </button>
    </div>
</div>
