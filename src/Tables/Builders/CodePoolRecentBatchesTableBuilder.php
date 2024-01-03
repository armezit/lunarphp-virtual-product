<?php

namespace Armezit\Lunar\VirtualProduct\Tables\Builders;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Lunar\Hub\Tables\TableBuilder;

class CodePoolRecentBatchesTableBuilder extends TableBuilder
{
    /**
     * Return the query data.
     */
    public function getData(): iterable
    {
        return CodePoolBatch::query()
            ->where('status', CodePoolBatchStatus::Running->value)
            ->orWhereDate('created_at', '>=', now()->sub('1 hour'))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
}
