<?php

namespace Armezit\GetCandy\VirtualProduct\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Armezit\GetCandy\VirtualProduct\GetCandyVirtualProduct
 */
class VirtualProduct extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Armezit\GetCandy\VirtualProduct\VirtualProductManager::class;
    }
}
