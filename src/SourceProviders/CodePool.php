<?php

namespace Armezit\Lunar\VirtualProduct\SourceProviders;

use Armezit\Lunar\VirtualProduct\Contracts\SourceProvider;
use Lunar\Models\Product;

class CodePool implements SourceProvider
{
    public function getName(): string
    {
        return __('lunarphp-virtual-product::code-pool.label');
    }

    public function getStock(): ?int
    {
        // TODO: implement me!
        return null;
    }

    public function getProductSettingsComponent(): ?string
    {
        return 'hub.lunarphp-virtual-product.components.code_pool.product-settings';
    }

    public function onProductSave(Product $product, array $data): void
    {
    }
}
