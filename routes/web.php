<?php

use Armezit\Lunar\VirtualProduct\Http\Livewire\Pages\CodePool\Import;
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
    ], function ($router) {
        /*
         * CodePool routes
         */
        Route::get('/code-pool/import', Import::class)->name('hub.virtual-product.code-pool.import');
    });
});
