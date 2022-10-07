<?php

namespace Armezit\Lunar\VirtualProduct\Sources;

use Armezit\Lunar\VirtualProduct\Contracts\SourceProvider;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
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

    public function saveProductSettings(Product $product, array $data): void
    {
        $codePoolSchema = CodePoolSchema::where(['product_id' => $product->id])->firstOrNew();
        $codePoolSchema->product_id = $product->id;
        $codePoolSchema->schema = $data;
        $codePoolSchema->save();
    }
}
