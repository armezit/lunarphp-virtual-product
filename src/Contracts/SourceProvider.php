<?php

namespace Armezit\Lunar\VirtualProduct\Contracts;

use Lunar\Models\Product;

interface SourceProvider
{
    public function getName(): string;

    public function getStock(): ?int;

    /**
     * Get livewire component name which should be displayed on product edit page
     */
    public function getProductSettingsComponent(): ?string;

    /**
     * Save
     */
    public function onProductSave(Product $product, array $data): void;
}
