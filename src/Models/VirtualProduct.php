<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Product;

/**
 * @property int product_id
 * @property string $source
 * @property ArrayObject $meta
 * @property-read Product $product
 */
class VirtualProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'source',
        'meta',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'meta' => AsArrayObject::class,
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('lunarphp-virtual-product.virtual_products_table');
    }

    /**
     * Scope a query to only include virtual products which have CodePool source
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyCodePool(Builder $query): Builder
    {
        return $query->whereSource(\Armezit\Lunar\VirtualProduct\SourceProviders\CodePool::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
