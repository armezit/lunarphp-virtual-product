<?php

namespace Armezit\GetCandy\VirtualProduct\Http\Livewire\Slots;

use Armezit\GetCandy\VirtualProduct\Contracts\SourceProvider;
use Armezit\GetCandy\VirtualProduct\Models\VirtualProduct;
use Armezit\GetCandy\VirtualProduct\Values\ProductSources;
use Armezit\GetCandy\VirtualProduct\Values\Source;
use GetCandy\Hub\Slots\AbstractSlot;
use GetCandy\Hub\Slots\Traits\HubSlot;
use GetCandy\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class VirtualProductSlot extends Component implements AbstractSlot
{
    use HubSlot;

    /**
     * @var bool
     */
    public bool $enabled = false;

    /**
     * @var ProductSources
     */
    public ProductSources $sources;

    /**
     * source provider class names
     * @var Collection
     */
    private Collection $sourceProviders;


    public static function getName()
    {
        return 'hub.getcandy-virtual-product.slots.virtual-product-slot';
    }

    public function getSlotHandle()
    {
        return 'virtual-product';
    }

    public function getSlotInitialValue()
    {
        return [
        ];
    }

    public function getSlotPosition()
    {
        return 'bottom';
    }

    public function getSlotTitle()
    {
        return __('getcandy-virtual-product::slots.virtual-product.title');
    }

    public function render()
    {
        return view('getcandy-virtual-product::livewire.slots.virtual-product');
    }

    protected function rules()
    {
        return [
            'sources.*.enabled' => 'nullable|boolean',
            'sources.*.class' => [
                'required_if:sources.*.enabled,1',
                Rule::in($this->getSourceProviders()),
            ],
            'sources.*.data' => [
                'required_if:sources.*.enabled,1',
                'bail',
                'array',
                'min:1',
            ],
            'sources.*.data.*' => 'string|distinct|min:1',
        ];
    }

    protected function getListeners()
    {
        return [
            'sourceUpdated' => 'onSourceDataUpdated'
        ];
    }

    public function mount()
    {
        $this->initSources();
    }

    public function getSourceProviders(): Collection
    {
        if (!isset($this->sourceProviders)) {
            $this->sourceProviders = collect(config('getcandy-virtual-product.sources', []));
        }
        return $this->sourceProviders;
    }

    /**
     * Init Source provider instances
     * @return ProductSources
     */
    private function initSources(): ProductSources
    {
        // for new products, all sources enabled by default.
        // for existing products, read their enabled sources from db.
        if (!$this->slotModel || !$this->slotModel->exists) {
            $enabledSources = $this->getSourceProviders();
        } else {
            $enabledSources = VirtualProduct::firstOrNew(['product_id' => $this->slotModel->id])
                ->sources
                ->toArray();

            // if existing product has virtual-product sources, enable the whole slot
            if (count($enabledSources) > 0) {
                $this->enabled = true;
            }
        }

        $this->sources = new ProductSources(
            sources: $this->getSourceProviders()
                ->map(fn(string $sourceProvider) => new Source(
                    $sourceProvider,
                    in_array($sourceProvider, $enabledSources, true)),
                )
                ->toArray(),
        );

        return $this->sources;
    }

    /**
     * Keep source data to be used later on saving slot
     * @param mixed $payload
     * @return void
     */
    public function onSourceDataUpdated(mixed $payload)
    {
        $sourceClass = $payload['source'];
        $sourceData = $payload['data'];
        $this->sources->setSourceData($sourceClass, $sourceData);

        $this->updateSlotData();
    }

    public function updated()
    {
        $this->updateSlotData();
    }

    private function updateSlotData()
    {
        try {
            $validatedData = $this->validate();
            $this->saveSlotData($validatedData);
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        }
    }

    public function updateSlotModel()
    {
    }

    /**
     * @param Product $model
     * @param array $data
     * @return \Illuminate\Support\MessageBag|void
     */
    public function handleSlotSave($model, $data)
    {
        $validator = Validator::make($data, $this->rules());
        if ($validator->fails()) {
            return $validator->getMessageBag();
        }

        $this->slotModel = $model;

        DB::transaction(function () use ($model, $validator) {
            $validatedData = $validator->validated();
            $enabledSources = collect($validatedData['sources'])->whereStrict('enabled', true);

            // save each source provider data
            foreach ($enabledSources as $payload) {
                /** @var SourceProvider $sourceProvider */
                $sourceProvider = app($payload['class']);
                $sourceProvider->saveProductSettings($model, $payload['data']);
            }

            // save product enabled sources
            $virtualProduct = VirtualProduct::where(['product_id' => $model->id])->firstOrNew();
            $virtualProduct->product_id = $model->id;
            $virtualProduct->sources = $enabledSources->pluck(['class'])->toArray();
            $virtualProduct->save();
        });
    }

}
