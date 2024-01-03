<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Schemas;

use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaField;
use Armezit\Lunar\VirtualProduct\Data\CodePoolSchemaFieldsList;

/**
 * @property-read bool $canDelete
 */
class SchemaShow extends AbstractSchema
{
    /**
     * Defines the confirmation text when deleting a language.
     */
    public ?string $deleteConfirm = null;

    /**
     * Called when we mount the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->fields = new CodePoolSchemaFieldsList(
            fields: CodePoolSchemaField::collection($this->schema->fields)
        );
    }

    /**
     * Soft deletes a schema.
     *
     * @return void
     */
    public function delete()
    {
        if (! $this->canDelete) {
            return;
        }

        $this->schema->delete();

        $this->notify(
            'Code pool schema successfully deleted.',
            'hub.virtual-products.code-pool.schemas.index'
        );
    }

    /**
     * Returns whether we have met the criteria to allow deletion.
     */
    public function getCanDeleteProperty(): bool
    {
        return $this->deleteConfirm === $this->schema->name;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.schemas.show')
            ->layout('adminhub::layouts.base');
    }
}
