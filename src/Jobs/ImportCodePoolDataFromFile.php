<?php

namespace Armezit\Lunar\VirtualProduct\Jobs;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolBatchStatus;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Armezit\Lunar\VirtualProduct\Repository\SpreadsheetFileReader;
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
use Throwable;

class ImportCodePoolDataFromFile implements ShouldQueue
{
    use Batchable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public CodePoolBatch $codePoolBatch,
        public CodePoolSchema $codePoolSchema,
        public array $columnsToMap,
        public string $filepath,
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

        $readerIterator = (new SpreadsheetFileReader($this->filepath))->getRecordsIterator();
        $records = LazyCollection::make(function () use ($readerIterator) {
            foreach ($readerIterator as $record) {
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

        $batch = Bus::batch($jobs)
            ->name(sprintf('Code pool import: purchasable_id=%s', $this->codePoolBatch->purchasable_id))
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
            })
            ->dispatch();

        // associate the job with subject model
        // NOTE: wait for lunarphp 0.8
        //\Lunar\Models\JobBatch::find($batch->id)
        //    ?->subject()
        //    ->associate($this->codePoolSchema)
        //    ->save();
    }
}
