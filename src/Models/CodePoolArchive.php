<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolArchiveFactory;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\OrderLine;

/**
 * @property int $batch_id
 * @property int $schema_id
 * @property int $order_line_id
 * @property ArrayObject $data
 * @property-read CodePoolBatch $batch
 * @property-read CodePoolSchema $schema
 * @property-read OrderLine $orderLine
 */
class CodePoolArchive extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
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
        return config('lunarphp-virtual-product.code_pool.archive_table');
    }

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CodePoolArchiveFactory
    {
        return CodePoolArchiveFactory::new();
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
     * Get the order line that owns the item.
     */
    public function orderLine()
    {
        return $this->belongsTo(OrderLine::class, 'order_line_id');
    }
}
