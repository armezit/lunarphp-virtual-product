<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Lunar\Hub\Http\Livewire\Traits\Notifies;

/**
 * @property-read bool $canModify
 */
abstract class AbstractSchema extends Component
{
    use Notifies;

    /**
     * The current schema we're showing.
     */
    public CodePoolSchema $schema;

    /**
     * The schema fields.
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

    public function addField(?string $name = null, ?string $type = null): void
    {
        $this->fields[] = new CodePoolSchemaField(
            name: $name,
            type: $type !== null ? CodePoolFieldType::from($type) : CodePoolFieldType::Raw,
            order: count($this->fields) + 1
        );
    }

    public function updatingFields(&$value, $name): void
    {
        if (preg_match('/(\d+)\.(.+)/', $name, $matches) !== false) {
            if (isset($matches[2]) && $matches[2] === 'type') {
                $value = CodePoolFieldType::from($value);
            }
        }
    }

    /**
     * Sort schema fields.
     *
     * @return void
     */
    public function sortFields(array $fields)
    {
        $sortedFields = collect();

        $items = collect($fields['items']);

        foreach ($this->fields as $value) {
            // Get the new position
            $item = $items->first(
                fn ($item) => $item['id'] == $value->name
            );

            $value->order = $item['order'];
            $sortedFields->push($value);
        }

        $this->fields = new CodePoolSchemaFieldsList(
            fields: CodePoolSchemaField::collection($sortedFields->sortBy('order')->values()->toArray())
        );
    }

    /**
     * Returns whether we can update/delete schema
     */
    public function getCanModifyProperty(): bool
    {
        return $this->schema->items()->count() < 1 && $this->schema->archivedItems()->count() < 1;
    }

    /**
     * Validates the LiveWire request, updates the model and dispatches and event.
     */
    public function save(): void
    {
        $this->validate();

        $notifyMessage = ! $this->schema->exists ?
            'Code pool schema successfully created.' :
            'Code pool schema successfully updated.';

        $this->schema->fields = collect($this->fields);
        $this->schema->save();

        $this->notify($notifyMessage, 'hub.virtual-products.code-pool.schemas.index');
    }
}
