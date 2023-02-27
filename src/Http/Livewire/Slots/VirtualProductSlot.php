<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Slots;

use Armezit\Lunar\VirtualProduct\Contracts\SourceProvider;
use Armezit\Lunar\VirtualProduct\Data\ProductSource;
use Armezit\Lunar\VirtualProduct\Data\ProductSourcesList;
use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Lunar\Hub\Slots\AbstractSlot;
use Lunar\Hub\Slots\Traits\HubSlot;
use Lunar\Models\Product;

/**
 * @property-read Collection $sourceProviders
 */
class VirtualProductSlot extends Component implements AbstractSlot
{
    use HubSlot;

    public bool $enabled = false;

    public ProductSourcesList $sources;

    public static function getName()
    {
        return 'hub.lunarphp-virtual-product.slots.virtual-product-slot';
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
        return __('lunarphp-virtual-product::slots.virtual-product.title');
    }

    public function render()
    {
        return view('lunarphp-virtual-product::livewire.slots.virtual-product');
    }

    protected function rules()
    {
        return [
            'sources.*.enabled' => 'nullable|boolean',
            'sources.*.class' => [
                'required_if:sources.*.enabled,1',
                Rule::in($this->sourceProviders),
            ],
            'sources.*.meta' => [
                'required_if:sources.*.enabled,1',
                'bail',
                'array',
            ],
        ];
    }

    protected function getListeners()
    {
        return [
            'sourceUpdated' => 'onSourceDataUpdated',
        ];
    }

    public function mount()
    {
        $this->initSources();
    }

    public function getSourceProvidersProperty(): Collection
    {
        return collect(config('lunarphp-virtual-product.sources', []));
    }

    /**
     * Init Source provider instances
     */
    private function initSources()
    {
        // for existing products, read their enabled sources from db.
        // if no virtual product exists for the current product (either new or existing product),
        // enable all sources by default

        if ($this->slotModel && $this->slotModel->exists) {
            $virtualProducts = VirtualProduct::where('product_id', $this->slotModel->id)->get();

            if ($virtualProducts->count() > 0) {
                $enabledSources = $virtualProducts
                    ->mapWithKeys(fn($vp) => [$vp['source'] => $vp['meta']])
                    ->toArray();

                // enable the virtual-product slot
                $this->enabled = true;
            }
        }

        // for all other products, mark all sources as enabled but don't enable slot
        if (!isset($enabledSources)) {
            $enabledSources = $this->sourceProviders->toArray();
        }

        $this->sources = new ProductSourcesList(
            sources: ProductSource::collection(
                $this->sourceProviders
                    ->map(fn(string $sourceProvider) => new ProductSource(
                        class: $sourceProvider,
                        enabled: array_key_exists($sourceProvider, $enabledSources),
                        meta: $enabledSources[$sourceProvider] ?? []
                    ))
                    ->all()
            )
        );
    }

    /**
     * Keep source data to be used later on saving slot
     *
     * @param mixed $payload
     * @return void
     */
    public function onSourceDataUpdated(mixed $payload)
    {
        $this->sources->sourceMeta($payload['source'], $payload['data']);
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

        $validatedData = $validator->validated();

        if (!isset($validatedData['sources'])) {
            return;
        }

        DB::transaction(function () use ($validatedData, $model) {
            $enabledSources = collect($validatedData['sources'])->where('enabled', true);

            // save enabled source provider(s)
            foreach ($enabledSources as $source) {
                /** @var SourceProvider $sourceProvider */
                $sourceProvider = app($source['class']);
                $sourceProvider->onProductSave($model, $source['meta']);

                // save virtual product for the enabled source
                $virtualProduct = VirtualProduct::where([
                    'product_id' => $model->id,
                    'source' => $source['class'],
                ])->firstOrNew();

                $virtualProduct->product_id = $model->id;
                $virtualProduct->source = $source['class'];
                $virtualProduct->meta = $source['meta'];

                $virtualProduct->save();
            }

            // delete disabled source provider(s)
            $disabledSources = $this->sourceProviders->diff($enabledSources->pluck('class'));
            VirtualProduct::where('product_id', $model->id)
                ->whereIn('source', $disabledSources)
                ->delete();
        });
    }
}
