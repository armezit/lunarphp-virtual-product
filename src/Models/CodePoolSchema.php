<?php

namespace Armezit\Lunar\VirtualProduct\Models;

use Armezit\Lunar\VirtualProduct\Database\Factories\CodePoolSchemaFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property array $fields
 */
class CodePoolSchema extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'fields' => AsCollection::class,
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
     */
    protected static function newFactory(): CodePoolSchemaFactory
    {
        return CodePoolSchemaFactory::new();
    }

    /**
     * Get schema items.
     */
    public function items()
    {
        return $this->hasMany(CodePoolItem::class, 'schema_id', 'id');
    }

    /**
     * Get schema archived items.
     */
    public function archivedItems()
    {
        return $this->hasMany(CodePoolArchive::class, 'schema_id', 'id');
    }
}
