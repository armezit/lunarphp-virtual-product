<div class="space-y-4">
    <div class="space-y-2">
        <x-hub::input.group label="{{ __('lunarphp-virtual-product::code-pool.product-settings.schema.label') }}"
                            for="schemaId">
            <x-hub::input.select wire:model="schemaId">
                <option value readonly>
                    {{ __('adminhub::fieldtypes.dropdown.empty_selection') }}
                </option>
                @foreach($this->schemas as $s)
                    <option value="{{ $s->id }}" @if($s->id == $this->schemaId) selected @endif>{{ $s->name }}</option>
                @endforeach
            </x-hub::input.select>
        </x-hub::input.group>
    </div>

    <div>
        <x-hub::button
            tag="a"
            href="{{ route('hub.virtual-products.code-pool.import', ['pid' => $this->product->id]) }}"
            class="space-x-2 rtl:space-x-reverse"
        >
            <x-hub::icon ref="cloud-upload" class="w-4"/>
            <span>
                {{ __('lunarphp-virtual-product::code-pool.product-settings.import_items_btn') }}
            </span>
        </x-hub::button>
    </div>
</div>
