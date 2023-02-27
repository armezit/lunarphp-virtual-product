<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolBatchFactory;
use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Purchasable;
use Lunar\Hub\Models\Staff;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;

/**
 * @property int $id
 * @property int $purchasable_id
 * @property string $purchasable_type
 * @property int $staff_id
 * @property string $status
 * @property float|null $entry_price
 * @property int|null $entry_price_currency_id
 * @property string|null $notes
 * @property ArrayObject $meta
 * @property-read Purchasable $purchasable
 * @property-read Staff $staff
 * @property-read Currency|null $entryPriceCurrency
 *
 * @method Builder forPurchasable(int $purchasableId)
 * @method Builder byStaff(int $staffId)
 */
class CodePoolBatch extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'meta' => AsArrayObject::class,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'meta' => '[]',
    ];

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

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

    /**
     * Return the staff member relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    /**
     * Returns the entry price currency relation.
     */
    public function entryPriceCurrency()
    {
        return $this->hasOne(Currency::class, 'id', 'entry_price_currency_id');
    }

    /**
     * Scope query to include only "completed" batches
     */
    public function scopeCompleted(Builder $builder): Builder
    {
        return $builder->where('status', CodePoolBatchStatus::Completed->value);
    }

    /**
     * Scope query to include only batches of specific purchasable
     */
    public function scopeForPurchasable(Builder $builder, int $purchasableId): Builder
    {
        return $builder->whereHasMorph(
            'purchasable',
            [ProductVariant::class],
            function (Builder $q) use ($purchasableId) {
                $q->where('id', $purchasableId);
            }
        );
    }

    /**
     * Scope query to include only batches of specific staff
     */
    public function scopeByStaff(Builder $builder, int $staffId): Builder
    {
        return $builder->where('staff_id', $staffId);
    }

    /**
     * Get the percentage of import completion
     */
    public function percentageComplete(): float
    {
        return floor(($this->meta['processed_rows'] / $this->meta['total_rows']) * 100);
    }
}
