<?php

namespace Armezit\GetCandy\VirtualProduct\Sources;

use Armezit\GetCandy\VirtualProduct\Contracts\SourceProvider;
use Armezit\GetCandy\VirtualProduct\Models\CodePoolSchema;
use GetCandy\Models\Product;

class CodePool implements SourceProvider
{
    public function getName(): string
    {
        return __('getcandy-virtual-product::code-pool.label');
    }

    public function getStock(): ?int
    {
        // TODO: implement me!
        return null;
    }

    public function getProductSettingsComponent(): ?string
    {
        return 'hub.getcandy-virtual-product.components.code_pool.product-settings';
    }

    public function saveProductSettings(Product $product, array $data): void
    {
        $codePoolSchema = CodePoolSchema::where(['product_id' => $product->id])->firstOrNew();
        $codePoolSchema->product_id = $product->id;
        $codePoolSchema->schema = $data;
        $codePoolSchema->save();
    }
}
