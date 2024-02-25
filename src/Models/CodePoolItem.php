<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Lunar\Base\Purchasable;
use Lunar\Models\ProductVariant;

/**
 * @property int $batch_id
 * @property int $schema_id
 * @property ArrayObject $data
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property-read CodePoolBatch $batch
 * @property-read CodePoolSchema $schema
 * @property-read Purchasable|ProductVariant $purchasable
 *
 * @method Builder forPurchasable(int $purchasableId)
 */
class CodePoolItem extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data' => AsArrayObject::class,
    ];

    protected $guarded = [];

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
     */
    protected static function newFactory(): CodePoolItemFactory
    {
        return CodePoolItemFactory::new();
    }

    /**
     * Get the code pool batch that owns the item.
     */
    public function batch()
    {
        return $this->belongsTo(CodePoolBatch::class, 'batch_id');
    }

    /**
     * Get the code pool batch that owns the item.
     */
    public function schema()
    {
        return $this->belongsTo(CodePoolSchema::class, 'schema_id');
    }

    /**
     * Return the polymorphic relation.
     */
    public function purchasable(): MorphTo
    {
        return $this->batch->morphTo();
    }

    /**
     * Scope query to include only items of specific purchasable
     */
    public function scopeForPurchasable(Builder $builder, int $purchasableId): Builder
    {
        return $builder->whereRelation('batch', function (Builder $q) use ($purchasableId) {
            return $q->forPurchasable($purchasableId);
        });
    }
}
