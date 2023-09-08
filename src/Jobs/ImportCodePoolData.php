<?php

namespace Armezit\Lunar\VirtualProduct\Jobs;

use Armezit\Lunar\VirtualProduct\Enums\CodePoolFieldType;
use Armezit\Lunar\VirtualProduct\Exceptions\FieldValidationException;
use Armezit\Lunar\VirtualProduct\Models\CodePoolBatch;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Armezit\Lunar\VirtualProduct\Models\CodePoolSchema;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ImportCodePoolData implements ShouldQueue
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
        public array $chunk,
        public array $columns,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws FieldValidationException
     */
    public function handle()
    {
        $records = $this->mapRecords();

        CodePoolItem::insert(
            collect($records)
                ->map(function (array $record) {
                    return [
                        'batch_id' => $this->codePoolBatch->id,
                        'schema_id' => $this->codePoolSchema->id,
                        'data' => json_encode($record),
                        'created_at' => time(),
                        'updated_at' => time(),
                    ];
                })
                ->toArray()
        );

        $this->codePoolBatch->meta['processed_rows'] = count($records);
        $this->codePoolBatch->save();
    }

    /**
     * @throws FieldValidationException
     */
    private function mapRecords(): array
    {
        $data = [];
        foreach ($this->chunk as $record) {
            $row = [];
            foreach ($this->codePoolSchema->fields as $field) {
                $fieldName = $field['name'];
                $fieldValue = $record[$this->columns[$fieldName]];

                $row[$fieldName] = $fieldValue;

                if (! $this->validateField(CodePoolFieldType::from($field['type']), $fieldValue)) {
                    throw new FieldValidationException(
                        sprintf(
                            "field validation error for field '%s' in data record: [%s]",
                            $fieldName,
                            Str::limit(implode(',', $record), end: '')
                        )
                    );
                }
            }
            $data[] = $row;
        }

        return $data;
    }

    private function validateField(CodePoolFieldType $type, mixed $value)
    {
        $filter = match ($type) {
            CodePoolFieldType::Raw => FILTER_DEFAULT,
            CodePoolFieldType::Integer => FILTER_VALIDATE_INT,
            CodePoolFieldType::Float => FILTER_VALIDATE_FLOAT,
            CodePoolFieldType::Email => FILTER_VALIDATE_EMAIL,
            CodePoolFieldType::Url => FILTER_VALIDATE_URL,
        };

        return filter_var($value, $filter);
    }
}
