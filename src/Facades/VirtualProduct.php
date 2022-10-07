<?php

namespace Armezit\Lunar\VirtualProduct\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Armezit\Lunar\VirtualProduct\LunarVirtualProduct
 */
class VirtualProduct extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Armezit\Lunar\VirtualProduct\VirtualProductManager::class;
    }
}
