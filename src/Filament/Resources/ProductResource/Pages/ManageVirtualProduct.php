<?php

namespace Armezit\Lunar\VirtualProduct\Filament\Resources\ProductResource\Pages;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\SourceProviders\CodePool;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;

class ManageVirtualProduct extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    public bool $virtual_enabled = false;

    public ?int $code_pool_schema_id = null;

    public function getTitle(): string|Htmlable
    {
        return __('lunarphp-virtual-product::default.virtual-product');
    }

    public static function getNavigationLabel(): string
    {
        return __('lunarphp-virtual-product::default.virtual-product');
    }

    public function getBreadcrumb(): string
    {
        return __('lunarphp-virtual-product::default.virtual-product');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::product-variants');
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $virtualProduct = $this->getCodePoolVirtualProduct();

        $this->virtual_enabled = $virtualProduct !== null;
        $this->code_pool_schema_id = $virtualProduct?->meta['schemaId'] ?? null;
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('lunarphp-virtual-product::default.virtual-product'))
                    ->schema([
                        Toggle::make('virtual_enabled')
                            ->label(__('lunarphp-virtual-product::default.enabled'))
                            ->live(),

                        Select::make('code_pool_schema_id')
                            ->label(__('lunarphp-virtual-product::code-pool.product-settings.schema.label'))
                            ->options(fn (): array => CodePoolSchema::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => (bool) $get('virtual_enabled'))
                            ->required(fn (Get $get): bool => (bool) $get('virtual_enabled'))
                            ->createOptionForm(static::getCodePoolSchemaForm())
                            ->createOptionUsing(fn (array $data): int => static::createCodePoolSchema($data)->getKey())
                            ->editOptionForm(static::getCodePoolSchemaForm())
                            ->getSelectedRecordUsing(fn (Select $component): ?CodePoolSchema => filled($component->getState())
                                ? CodePoolSchema::query()->find($component->getState())
                                : null)
                            ->fillEditOptionActionFormUsing(fn (Select $component): ?array => static::getCodePoolSchemaFormData($component->getSelectedRecord()))
                            ->updateOptionUsing(function (Select $component, array $data): void {
                                static::updateCodePoolSchema($component->getSelectedRecord(), $data);
                            }),
                    ]),
            ])
            ->statePath('');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $this->virtual_enabled) {
            $this->getCodePoolVirtualProduct()?->delete();

            return $record;
        }

        VirtualProduct::query()->updateOrCreate([
            'product_id' => $record->getKey(),
            'source' => CodePool::class,
        ], [
            'meta' => [
                'schemaId' => $this->code_pool_schema_id,
            ],
        ]);

        return $record;
    }

    public function getRelationManagers(): array
    {
        return [];
    }

    protected function getCodePoolVirtualProduct(): ?VirtualProduct
    {
        return VirtualProduct::query()
            ->onlyCodePool()
            ->where('product_id', $this->getRecord()->getKey())
            ->first();
    }

    protected static function getCodePoolSchemaForm(): array
    {
        return [
            TextInput::make('name')
                ->label(__('lunarphp-virtual-product::code-pool.partials.forms.schema.name'))
                ->required()
                ->maxLength(255),

            Repeater::make('fields')
                ->label(__('lunarphp-virtual-product::code-pool.partials.forms.schema.data_fields'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('lunarphp-virtual-product::code-pool.partials.forms.schema.field_name'))
                        ->required()
                        ->maxLength(255),

                    Select::make('type')
                        ->label(__('lunarphp-virtual-product::code-pool.partials.forms.schema.field_type'))
                        ->options(CodePoolFieldType::labels())
                        ->default(CodePoolFieldType::Raw->value)
                        ->required()
                        ->selectablePlaceholder(false),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->reorderable()
                ->addActionLabel(__('lunarphp-virtual-product::code-pool.partials.forms.schema.add_field'))
                ->minItems(1)
                ->required(),
        ];
    }

    protected static function createCodePoolSchema(array $data): CodePoolSchema
    {
        $schema = new CodePoolSchema;

        return static::updateCodePoolSchema($schema, $data);
    }

    protected static function updateCodePoolSchema(?CodePoolSchema $schema, array $data): CodePoolSchema
    {
        $schema ??= new CodePoolSchema;

        $schema->name = $data['name'];
        $schema->fields = collect($data['fields'])
            ->values()
            ->map(fn (array $field, int $index): array => [
                'name' => $field['name'],
                'type' => $field['type'],
                'order' => $index + 1,
            ]);

        $schema->save();

        return $schema;
    }

    protected static function getCodePoolSchemaFormData(?CodePoolSchema $schema): ?array
    {
        if (! $schema) {
            return null;
        }

        return [
            'name' => $schema->name,
            'fields' => $schema->fields
                ->map(fn (array $field): array => [
                    'name' => $field['name'],
                    'type' => $field['type'] instanceof CodePoolFieldType ? $field['type']->value : $field['type'],
                ])
                ->values()
                ->all(),
        ];
    }
}
