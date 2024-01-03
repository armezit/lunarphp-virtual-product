<?php

namespace Armezit\Lunar\VirtualProduct\Tables\Builders;

use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Lunar\Hub\Tables\TableBuilder;

class CodePoolBatchesTableBuilder extends TableBuilder
{
    /**
     * Return the query data.
     */
    public function getData(): iterable
    {
        $query = CodePoolBatch::query();

        if ($this->searchTerm) {
            $query->whereIn('id', CodePoolBatch::search("%{$this->searchTerm}%")->keys());
        }

        $filters = collect($this->queryStringFilters)->filter(function ($value) {
            return (bool) $value;
        });

        foreach ($this->queryExtenders as $qe) {
            call_user_func($qe, $query, $this->searchTerm, $filters);
        }

        // Get the table filters we want to apply.
        $tableFilters = $this->getFilters()->filter(function ($filter) use ($filters) {
            return $filters->has($filter->field);
        });

        foreach ($tableFilters as $filter) {
            call_user_func($filter->getQuery(), $filters, $query);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }
}
