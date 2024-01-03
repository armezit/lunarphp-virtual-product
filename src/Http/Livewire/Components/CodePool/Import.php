<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Exceptions\ReaderException;
use Armezit\Lunar\VirtualProduct\Jobs\ImportCodePoolDataFromFile;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Armezit\Lunar\VirtualProduct\Repository\SpreadsheetFileReader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Models\Staff;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;

/**
 * @property-read array $products
 * @property-read array $productVariants
 * @property-read array $currencies
 */
class Import extends Component
{
    use Notifies;
    use WithFileUploads;

    protected $queryString = [
        'productId' => ['except' => '', 'as' => 'pid'],
        'productVariantId' => ['except' => '', 'as' => 'vid'],
    ];

    public ?string $productId = null;

    public ?string $productVariantId = null;

    public array $currencies = [];

    public ?int $defaultCurrencyId = null;

    public CodePoolBatch $batch;

    public ?CodePoolSchema $schema = null;

    public ?Collection $schemaFields = null;

    public bool $showUploadSection = false;

    public array $columnsToMap = [];

    public array $fileHeaders = [];

    public int $fileRecordsCount = 0;

    /** @var \Livewire\TemporaryUploadedFile|string */
    public $file;

    public array $allowedFiletypes = [
        'application/vnd.ms-excel',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
    ];

    /**
     * @return void
     */
    public function mount()
    {
        if (! array_key_exists($this->productId, $this->products)) {
            $this->productId = null;
        }

        if (! array_key_exists($this->productVariantId, $this->productVariants)) {
            $this->productVariantId = null;
        }

        $this->batch = new CodePoolBatch();

        $this->initCurrencies();
        $this->setSchemaFields();
        $this->resetUploadSection();
    }

    public function initCurrencies(): void
    {
        $currencies = Currency::get();

        $this->defaultCurrencyId = $currencies->first(fn ($currency) => $currency->default === true)?->id;

        $this->currencies = $currencies
            ->mapWithKeys(fn ($c) => [$c->id => $c->code])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function getProductsProperty(): array
    {
        return VirtualProduct::onlyCodePool()
            ->with('product')
            ->get()
            ->mapWithKeys(fn (VirtualProduct $vp) => [
                $vp->product->id => $vp->product->translateAttribute('name'),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function getProductVariantsProperty(): array
    {
        if ((int) $this->productId <= 0) {
            return [];
        }

        return ProductVariant::where(['product_id' => $this->productId])
            ->with('product')
            ->get()
            ->mapWithKeys(fn (ProductVariant $v) => [$v->id => $v->getOption()])
            ->all();
    }

    /**
     * Returns whether we have met the criteria to allow import.
     *
     * @return bool
     */
    public function getCanImportProperty()
    {
        $nonEmptyMappedColumnsCount = collect(array_values($this->columnsToMap))
            ->filter(fn (string $value) => ! blank($value))
            ->count();

        return $nonEmptyMappedColumnsCount > 0 && count($this->fileHeaders) === $nonEmptyMappedColumnsCount;
    }

    public function updatedProductId()
    {
        $this->productVariantId = null;
        $this->setSchemaFields();
        $this->resetUploadSection();
    }

    public function updatedProductVariantId()
    {
        $this->resetUploadSection();
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        $this->extractDataFileProperties();

        $this->resetValidation();
    }

    public function removeFile()
    {
        unset($this->file);
        $this->fileHeaders = [];
    }

    private function resetUploadSection()
    {
        $this->showUploadSection = (int) $this->productVariantId > 0;
        $this->removeFile();
    }

    private function setSchemaFields()
    {
        if (blank($this->productId)) {
            return;
        }

        $virtualProduct = VirtualProduct::onlyCodePool()
            ->where(['product_id' => $this->productId])
            ->first();

        if (! $virtualProduct) {
            return;
        }

        $schemaId = $virtualProduct->meta['schemaId'];
        $this->schema = CodePoolSchema::find($schemaId);
        $this->schemaFields = collect($this->schema->fields)->sortBy('order');

        $this->columnsToMap = $this->schemaFields
            ->pluck('name')
            ->mapWithKeys(fn ($field) => [$field => ''])
            ->toArray();
    }

    private function extractDataFileProperties()
    {
        try {
            $reader = new SpreadsheetFileReader($this->file->getRealPath());
            $this->fileHeaders = $reader->getHeader();
            $this->fileRecordsCount = $reader->getRecordsCount();
        } catch (ReaderException $e) {
            Log::warning($e->getMessage());

            return $this->addError(
                'file',
                __('The file has error(s). Please check and try again')
            );
        }
    }

    public function import(): void
    {
        $this->importData();
        $this->notify('New import job started in the background.');
        $this->resetUploadSection();
        $this->emitSelf('$refresh');
    }

    protected function importData(): void
    {
        /** @var Staff $staff */
        $staff = Auth::guard('staff')->user();

        $this->batch->purchasable_type = ProductVariant::class;
        $this->batch->purchasable_id = (int) $this->productVariantId;
        $this->batch->staff_id = $staff->id;
        $this->batch->status = CodePoolBatchStatus::Running->value;

        // store entry_price_currency_id only if entry_price_currency has value
        if ($this->batch->entryPriceCurrency === null) {
            $this->batch->entry_price_currency_id = null;
        }

        $this->validate();

        $this->batch->save();

        ImportCodePoolDataFromFile::dispatch(
            $this->batch,
            $this->schema,
            $this->columnsToMap,
            $this->file->getRealPath(),
        );
    }

    private function formatFileSize(int $size, int $precision = 2): string|int
    {
        if ($size <= 0) {
            return $size;
        }
        $base = log($size) / log(1024);
        $suffixes = ['KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
    }

    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.import', [
            'fileSize' => $this->formatFileSize(config('lunarphp-virtual-product.code_pool.import.max_upload_size')),
        ]);
    }

    protected function rules()
    {
        $maxUploadSize = config('lunarphp-virtual-product.code_pool.import.max_upload_size');

        return [
            'batch.entry_price' => 'nullable|numeric',
            'batch.entry_price_currency_id' => 'nullable|integer',
            'batch.notes' => 'nullable|string',
            'productId' => 'required|integer',
            'productVariantId' => 'required|integer',
            'columnsToMap' => 'required|array|min:1',
            'file' => 'required|file|mimes:csv,ods,txt,xls,xlsx|max:'.$maxUploadSize,
        ];
    }

    protected function messages()
    {
        return [
            'productId' => __('lunarphp-virtual-product::validation.import.product_id'),
            'productVariantId' => __('lunarphp-virtual-product::validation.import.product_variant_id'),
            'columnsToMap' => __('lunarphp-virtual-product::validation.import.columns_to_map'),
            'file' => __('lunarphp-virtual-product::validation.import.file'),
        ];
    }
}
