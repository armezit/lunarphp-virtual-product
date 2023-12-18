<?php

namespace Armezit\Lunar\VirtualProduct\Jobs;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use League\Csv\Reader;
use Throwable;

class ImportCodePoolDataFromCsvFile implements ShouldQueue
{
    use Batchable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public CodePoolBatch  $codePoolBatch,
        public CodePoolSchema $codePoolSchema,
        public array          $columnsToMap,
        public string         $csvFilePath,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function handle()
    {
        $chunkSize = config('lunarphp-virtual-product.code_pool.import.chunk_size', 10);

        $records = LazyCollection::make(function () {
            foreach ($this->getCsvDataReader() as $record) {
                yield $record;
            }
        });

        $jobs = $records
            ->chunk($chunkSize)
            ->map(function ($chunk) {
                return new ImportCodePoolData(
                    $this->codePoolBatch,
                    $this->codePoolSchema,
                    $chunk,
                    $this->columnsToMap
                );
            });

        // NOTE: "$this" is not allowed in serialized closures
        $codePoolBatchId = $this->codePoolBatch->id;

        Bus::batch($jobs)
            ->name(sprintf('Code pool csv import: purchasable_id=%s', $this->codePoolBatch->purchasable_id))
            ->withOption('tags', ['Virtual Product'])
            ->then(function (Batch $batch) use ($codePoolBatchId) {
                // All jobs completed successfully...

                CodePoolBatch::where('id', $codePoolBatchId)->update([
                    'status' => CodePoolBatchStatus::Completed->value,
                ]);
            })->catch(function (Batch $batch, Throwable $e) use ($codePoolBatchId) {
                // First batch job failure detected...

                DB::transaction(function () use ($codePoolBatchId) {
                    CodePoolBatch::where('id', $codePoolBatchId)->update([
                        'status' => CodePoolBatchStatus::Failed->value,
                    ]);
                    CodePoolItem::where('batch_id', $codePoolBatchId)->delete();
                });
            })->finally(function (Batch $batch) {
                // The batch has finished executing...
                $a = 1;
            })
            ->dispatch();
    }

    private function getCsvDataReader(): Reader
    {
        return Reader::createFromPath($this->csvFilePath)
            ->setHeaderOffset(0)
            ->skipEmptyRecords();
    }
}
