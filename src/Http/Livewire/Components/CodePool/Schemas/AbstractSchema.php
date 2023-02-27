<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Spatie\LaravelData\DataCollection;

/**
 * @property-read bool $canModify
 */
abstract class AbstractSchema extends Component
{
    use Notifies;

    /**
     * The current schema we're showing.
     *
     * @var CodePoolSchema
     */
    public CodePoolSchema $schema;

    /**
     * The schema fields.
     *
     * @var CodePoolSchemaFieldsList
     */
    public CodePoolSchemaFieldsList $fields;

    /**
     * Returns validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'schema.name' => 'required|string|max:255',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string',
            'fields.*.type' => ['required', new Enum(CodePoolFieldType::class)],
        ];
    }

    protected function messages()
    {
        return [
            'fields.*.name.required' => __('lunarphp-virtual-product::code-pool.validation.field_name_required'),
            'fields.*.name' => __('lunarphp-virtual-product::code-pool.validation.field_type'),
        ];
    }

    public function getFieldTypesProperty()
    {
        return CodePoolFieldType::labels();
    }

    public function addField()
    {
        $this->fields[] = new CodePoolSchemaField(
            name: null,
            type: CodePoolFieldType::Raw,
            order: count($this->fields) + 1
        );
    }

    /**
     * Sort schema fields.
     *
     * @param  array  $columns
     * @return void
     */
    public function sortFields(array $columns)
    {
        $cols = collect();

        $items = collect($columns['items']);

        foreach ($this->fields as $value) {
            // Get the new position
            $item = $items->first(
                fn ($item) => $item['id'] == $value->name
            );

            $value->order = $item['order'];
            $cols->push($value);
        }

        $this->fields = $cols->sortBy('order')->values()->toArray();
    }

    /**
     * Returns whether we can update/delete schema
     *
     * @return bool
     */
    public function getCanModifyProperty(): bool
    {
        return $this->schema->items()->count() < 1 && $this->schema->archivedItems()->count() < 1;
    }
}
