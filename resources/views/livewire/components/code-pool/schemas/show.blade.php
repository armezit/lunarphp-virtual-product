<div class="space-y-4">
    <header>
        <h1 class="text-xl font-bold text-gray-900 md:text-2xl dark:text-white">
            {{ $schema->name }}
        </h1>
    </header>

    <form action="#"
          method="POST"
          class="space-y-4"
          wire:submit.prevent="save">
        @include('lunarphp-virtual-product::partials.forms.code-pool-schema')

        @if ($this->canModify)
            <div class="bg-white border border-red-300 rounded shadow">
                <header class="px-6 py-4 text-red-700 bg-white border-b border-red-300 rounded-t">
                    {{ __('adminhub::inputs.danger_zone.title') }}
                </header>

                <div class="p-6 text-sm">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <strong>{{ __('lunarphp-virtual-product::code-pool.partials.forms.schema.delete_schema') }}</strong>

                            <p class="text-xs text-gray-600">
                                {{ __('lunarphp-virtual-product::code-pool.partials.forms.schema.schema_name_delete') }}</p>
                        </div>

                        <div class="col-span-9 lg:col-span-4">
                            <x-hub::input.text wire:model="deleteConfirm" />
                        </div>

                        <div class="col-span-3 text-right lg:col-span-2">
                            <x-hub::button :disabled="!$this->canDelete"
                                           wire:click="delete"
                                           type="button"
                                           theme="danger">{{ __('adminhub::global.delete') }}</x-hub::button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white border border-blue-300 rounded shadow">
                <header class="px-6 py-4 text-red-700 bg-white border-b border-red-300 rounded-t">Read-only Schema</header>
                <div class="p-6 text-sm">Can't delete or modify schema. It has either available or archived items.</div>
            </div>
        @endif
    </form>
</div>
