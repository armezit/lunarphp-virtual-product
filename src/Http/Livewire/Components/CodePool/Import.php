<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool;

use Armezit\Lunar\VirtualProduct\Models\VirtualProduct;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;
use Livewire\Component;

class Import extends Component
{
    use Notifies;

    protected $queryString = [
        'productId' => ['except' => '', 'as' => 'pid'],
        'productVariantId' => ['except' => '', 'as' => 'pvid'],
    ];

    /**
     * @var string
     */
    public string $productId = '';

    /**
     * @var string
     */
    public string $productVariantId = '';

    /**
     * @var array
     */
    public array $currencies = [];

    /**
     * @var int|null
     */
    public ?int $defaultCurrencyId = null;

    /**
     * @var array
     */
    public array $batch = [];

    /**
     * @var string
     */
    protected string $purchasableType = ProductVariant::class;

    /**
     * @return void
     */
    public function mount()
    {
        $this->productId = request()->query->has('pid') ? request()->query->get('pid') : '';
        $this->productVariantId = request()->query->has('pvid') ? request()->query->get('pvid') : '';

        $this->initCurrencies();
    }

    /**
     * @return array<string, string>
     */
    public function getProductsProperty(): array
    {
        return VirtualProduct::onlyCodePool()
            ->with('product')
            ->get()
            ->mapWithKeys(fn ($vp) => [
                $vp->product->id => $vp->product->translateAttribute('name'),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function getProductVariantsProperty(): array
    {
        if (blank($this->productId)) {
            return [];
        }

        return ProductVariant::where(['product_id' => $this->productId])
            ->with('product')
            ->get()
            ->mapWithKeys(fn ($v) => [
                $v->id => $v->translateAttribute('name') ?: $v->product->translateAttribute('name'),
            ])
            ->all();
    }

    /**
     * @return void
     */
    public function initCurrencies(): void
    {
        $currencies = Currency::get();

        $this->defaultCurrencyId = $currencies->first(fn ($currency) => $currency->default === true)->id;

        $this->currencies = $currencies
            ->mapWithKeys(fn ($c) => [$c->id => $c->code])
            ->all();
    }

    public function onProductChange()
    {
        $this->productVariantId = '';
    }

    public function render()
    {
        return view('lunarphp-virtual-product::livewire.components.code-pool.import');
    }
}
