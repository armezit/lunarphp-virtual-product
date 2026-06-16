<?php

namespace Armezit\Lunar\VirtualProduct\Lunar\Extensions;

use Armezit\Lunar\VirtualProduct\Filament\Resources\ProductResource\Pages\ManageVirtualProduct;

class ProductResourceExtension
{
    protected ?object $caller = null;

    public function setCaller(?object $caller): void
    {
        $this->caller = $caller;
    }

    /**
     * @param  array<class-string>  $pages
     * @return array<class-string>
     */
    public function extendSubNavigation(array $pages): array
    {
        return [
            ...$pages,
            ManageVirtualProduct::class,
        ];
    }

    /**
     * @param  array<string, mixed>  $pages
     * @return array<string, mixed>
     */
    public function extendPages(array $pages): array
    {
        return [
            ...$pages,
            'virtual-product' => ManageVirtualProduct::route('/{record}/virtual-product'),
        ];
    }
}
