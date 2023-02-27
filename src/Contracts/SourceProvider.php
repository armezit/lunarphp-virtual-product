<?php

namespace Armezit\Lunar\VirtualProduct\Contracts;

use Lunar\Models\Product;

interface SourceProvider
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int|null
     */
    public function getStock(): ?int;

    /**
     * Get livewire component name which should be displayed on product edit page
     *
     * @return string|null
     */
    public function getProductSettingsComponent(): ?string;

    /**
     * Save
     *
     * @param  Product  $product
     * @param  array  $data
     * @return void
     */
    public function onProductSave(Product $product, array $data): void;
}
