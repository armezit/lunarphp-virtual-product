<?php

use Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Import;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Schemas\SchemaCreate;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Schemas\SchemaShow;
use Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Schemas\SchemasIndex;
use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Middleware\Authenticate;

/*
 * Admin Hub Routes
 */
Route::group([
    'prefix' => config('lunar-hub.system.path', 'hub'),
    'middleware' => ['web'],
], function () {
    Route::group([
        'prefix' => 'virtual-product',
        'middleware' => [
            Authenticate::class,
            'can:catalogue:manage-products',
        ],
    ], function () {
        Route::get('/', Import::class)->name('hub.virtual-products.index');

        /*
         * CodePool routes
         */
        Route::group([
            'prefix' => 'code-pool',
        ], function ($router) {
            Route::get('/import', Import::class)->name('hub.virtual-products.code-pool.import');
            Route::get('/schemas', SchemasIndex::class)->name('hub.virtual-products.code-pool.schemas.index');
            Route::get('/schemas/create', SchemaCreate::class)->name('hub.virtual-products.code-pool.schemas.create');
            Route::get('/schemas/{schema}', SchemaShow::class)->name('hub.virtual-products.code-pool.schemas.show');
        });
    });
});
