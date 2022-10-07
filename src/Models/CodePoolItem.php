<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolItemFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodePoolItem extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'data' => AsArrayObject::class,
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('lunarphp-virtual-product.code_pool.items_table');
    }

    /**
     * Return a new factory instance for the model.
     *
     * @return CodePoolItemFactory
     */
    protected static function newFactory(): CodePoolItemFactory
    {
        return CodePoolItemFactory::new();
    }

    /**
     * Get the code pool batch that owns the item.
     */
    public function codePoolBatch()
    {
        return $this->belongsTo(CodePoolBatch::class, 'batch_id');
    }
}
