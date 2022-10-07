<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolBatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Casts\Price as CastsPrice;

class CodePoolBatch extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'entry_price' => CastsPrice::class,
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('lunarphp-virtual-product.code_pool.batches_table');
    }

    /**
     * Return a new factory instance for the model.
     *
     * @return CodePoolBatchFactory
     */
    protected static function newFactory(): CodePoolBatchFactory
    {
        return CodePoolBatchFactory::new();
    }

    /**
     * Return the polymorphic relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function purchasable()
    {
        return $this->morphTo();
    }
}
