<?php

namespace Armezit\GetCandy\VirtualProduct\Models;

use GetCandy\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class VirtualProduct extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'sources' => AsArrayObject::class,
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('getcandy-virtual-product.virtual_products_table');
    }

    /**
     * Scope a query to only include virtual products which have CodePool source
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOnlyCodePool(Builder $query): Builder
    {
        return $query->whereJsonContains(
            'sources',
            \Armezit\GetCandy\VirtualProduct\Sources\CodePool::class
        );
    }

    /**
     * Get the product that owns the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
