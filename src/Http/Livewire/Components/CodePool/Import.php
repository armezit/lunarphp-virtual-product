<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Jobs\ImportCodePoolDataFromCsvFile;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;
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

    public bool $showCsvImporter = false;

    public array $columnsToMap = [];

    public array $fileHeaders = [];

    public int $fileRowCount = 0;

    /** @var \Livewire\TemporaryUploadedFile|string */
    public $file;

    public array $allowedFiletypes = ['text/csv'];

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
        $this->resetImportSection();
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
        $this->batch->purchasable_id = 0;
        $this->setSchemaFields();
        $this->resetImportSection();
    }

    public function updatedBatchPurchasableId()
    {
        $this->resetImportSection();
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        $this->setCsvProperties();

        $this->resetValidation();
    }

    public function removeFile()
    {
        unset($this->file);
        $this->fileHeaders = [];
    }

    private function resetImportSection()
    {
        $this->showCsvImporter = (int) $this->productVariantId > 0;
        $this->removeFile();
    }

    private function setSchemaFields()
    {
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

    private function setCsvProperties()
    {
        try {
            $csv = Reader::createFromPath($this->file->getRealPath())
                ->setHeaderOffset(0)
                ->skipEmptyRecords();

            $records = Statement::create()->process($csv);

            $this->fileHeaders = $csv->getHeader();
            $this->fileRowCount = $records->count();
        } catch (\League\Csv\Exception $e) {
            Log::warning($e->getMessage());

            return $this->addError(
                'file',
                __('The file has error/errors, Please check, and try again')
            );
        }
    }

    public function import(): void
    {
        $this->importCsv();
        $this->resetImportSection();
        $this->emitSelf('$refresh');
    }

    protected function importCsv(): void
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

        ImportCodePoolDataFromCsvFile::dispatch(
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
            'batch.purchasable_id' => 'required|integer',
            'batch.entry_price' => 'nullable|numeric',
            'batch.entry_price_currency_id' => 'nullable|integer',
            'batch.notes' => 'nullable|string',
            'columnsToMap' => 'required|array|min:1',
            'file' => 'required|file|mimes:csv,txt|max:'.$maxUploadSize,
        ];
    }

    protected function messages()
    {
        return [
            'productId' => __('lunarphp-virtual-product::validation.import.product_id'),
            'batch.purchasable_id' => __('lunarphp-virtual-product::validation.import.product_variant_id'),
            'columnsToMap' => __('lunarphp-virtual-product::validation.import.columns_to_map'),
            'file' => __('lunarphp-virtual-product::validation.import.csv_file'),
        ];
    }
}
