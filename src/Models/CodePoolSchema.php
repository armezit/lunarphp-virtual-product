<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolSchemaFactory;
use Lunar\Models\Product;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodePoolSchema extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $casts = [
        'schema' => AsArrayObject::class,
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('lunarphp-virtual-product.code_pool.schema_table');
    }

    /**
     * Return a new factory instance for the model.
     *
     * @return CodePoolSchemaFactory
     */
    protected static function newFactory(): CodePoolSchemaFactory
    {
        return CodePoolSchemaFactory::new();
    }

    /**
     * Get the product that owns the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
