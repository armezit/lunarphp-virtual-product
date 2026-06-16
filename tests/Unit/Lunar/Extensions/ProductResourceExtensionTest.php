<?php

use Armezit\Lunar\VirtualProduct\Filament\Resources\ProductResource\Pages\ManageVirtualProduct;
use Armezit\Lunar\VirtualProduct\Lunar\Extensions\ProductResourceExtension;
use Lunar\Admin\Filament\Resources\ProductResource\Pages\EditProduct;

it('adds the virtual product page to Lunar product navigation', function () {
    $extension = new ProductResourceExtension;

    expect($extension->extendSubNavigation([EditProduct::class]))
        ->toBe([
            EditProduct::class,
            ManageVirtualProduct::class,
        ]);
});

it('adds the virtual product route to Lunar product pages', function () {
    if (! class_exists(EditProduct::class) || ! class_exists(ManageVirtualProduct::class)) {
        $this->markTestSkipped('Lunar Admin is not installed.');
    }

    $extension = new ProductResourceExtension;

    $pages = $extension->extendPages([
        'edit' => EditProduct::route('/{record}/edit'),
    ]);

    expect($pages)
        ->toHaveKey('edit')
        ->toHaveKey('virtual-product');
});
