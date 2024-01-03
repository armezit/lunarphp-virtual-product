<?php

namespace Armezit\Lunar\VirtualProduct\Http\Livewire\Components\CodePool\Batches;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Tables\Builders\CodePoolBatchesTableBuilder;
use Armezit\Lunar\VirtualProduct\Tables\Builders\CodePoolRecentBatchesTableBuilder;
use Armezit\Lunar\VirtualProduct\Tables\Builders\CodePoolSchemasTableBuilder;
use Illuminate\Support\Collection;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Models\SavedSearch;
use Lunar\Hub\Models\Staff;
use Lunar\Hub\Tables\TableBuilder;
use Lunar\LivewireTables\Components\Columns\ProgressColumn;
use Lunar\LivewireTables\Components\Columns\TextColumn;
use Lunar\LivewireTables\Components\Table;

/**
 * @property-read CodePoolSchemasTableBuilder $tableBuilder
 * @property-read Collection<SavedSearch> $savedSearches
 */
class BatchesTable extends Table
{
    use Notifies;

    /**
     * {@inheritDoc}
     */
    public bool $searchable = true;

    /**
     * {@inheritDoc}
     */
    public bool $canSaveSearches = true;

    /**
     * Only display list of running and recently executed import batches
     */
    public bool $onlyRecent = false;

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'saveSearch' => 'handleSaveSearch',
    ];

    public function getTableBuilderProperty(): TableBuilder
    {
        return $this->onlyRecent ?
            app(CodePoolRecentBatchesTableBuilder::class) :
            app(CodePoolBatchesTableBuilder::class);
    }

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $this->tableBuilder->baseColumns([
            TextColumn::make('name', function (CodePoolBatch $batch) {
                return
                    collect($batch->purchasable->product?->translateAttribute('name'))
                        ->concat($batch->purchasable->getOptions())
                        ->join(' | ');
            }),

            ProgressColumn::make('status')
                ->progress(function (CodePoolBatch $batch) {
                    return match ($batch->status) {
                        CodePoolBatchStatus::Running->value => $batch->getProgress(),
                        default => 100,
                    };
                })
                ->label(function (CodePoolBatch $batch) {
                    return match ($batch->status) {
                        CodePoolBatchStatus::Running->value => "{$batch->getProgress()}%",
                        CodePoolBatchStatus::Failed->value => 'Failed',
                        CodePoolBatchStatus::Completed->value => 'Completed',
                    };
                })
                ->color(function (CodePoolBatch $batch) {
                    return match ($batch->status) {
                        CodePoolBatchStatus::Running->value => 'primary',
                        CodePoolBatchStatus::Failed->value => 'danger',
                        CodePoolBatchStatus::Completed->value => 'success',
                    };
                }),

            TextColumn::make('total', fn (CodePoolBatch $batch) => $batch->meta['total'])
                ->heading(__('adminhub::global.no_items')),

            TextColumn::make('starts_at', fn (CodePoolBatch $batch) => $batch->created_at),

            TextColumn::make('causer.email', function (CodePoolBatch $batch) {
                return $batch->staff->firstname.' '.$batch->staff->lastname;
            }),

            TextColumn::make('entry_price', function (CodePoolBatch $batch) {
                return $batch->entry_price.' '.$batch->entryPriceCurrency?->code;
            })->heading(__('lunarphp-virtual-product::code-pool.import.input.entry_price')),

            TextColumn::make('notes', fn (CodePoolBatch $batch) => $batch->notes)
                ->heading(__('adminhub::global.notes')),
        ]);
    }

    /**
     * Remove a saved search record.
     *
     * @param  int  $id
     * @return void
     */
    public function deleteSavedSearch($id)
    {
        SavedSearch::destroy($id);

        $this->resetSavedSearch();

        $this->notify(
            __('adminhub::notifications.saved_searches.deleted')
        );
    }

    /**
     * Save a search.
     *
     * @return void
     */
    public function saveSearch()
    {
        $this->validateOnly('savedSearchName', [
            'savedSearchName' => 'required',
        ]);

        /** @var Staff $staff */
        $staff = auth()->getUser();

        $staff->savedSearches()
            ->create([
                'name' => $this->savedSearchName,
                'term' => $this->query,
                'component' => $this->getName(),
                'filters' => $this->filters,
            ]);

        $this->notify('Search saved');

        $this->savedSearchName = null;

        $this->emit('savedSearch');
    }

    /**
     * Return the saved searches available to the table.
     */
    public function getSavedSearchesProperty(): Collection
    {
        /** @var Staff $staff */
        $staff = auth()->getUser();

        return $staff->savedSearches()
            ->where('component', $this->getName())
            ->get()
            ->map(function ($savedSearch) {
                return [
                    'key' => $savedSearch->id,
                    'label' => $savedSearch->name,
                    'filters' => $savedSearch->filters,
                    'query' => $savedSearch->term,
                ];
            });
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        $filters = $this->filters;
        $query = $this->query;

        if ($this->savedSearch) {
            $search = $this->savedSearches->first(function ($search) {
                return $search['key'] == $this->savedSearch;
            });

            if ($search) {
                $filters = $search['filters'];
                $query = $search['query'];
            }
        }

        return $this->tableBuilder
            ->searchTerm($query)
            ->queryStringFilters($filters)
            ->perPage($this->perPage)
            ->getData();
    }
}
