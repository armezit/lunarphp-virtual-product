<?php

namespace Armezit\Lunar\VirtualProduct\Repository;

use Armezit\Lunar\VirtualProduct\Exceptions\ReaderException;
use Iterator;
use PhpOffice\PhpSpreadsheet;
use Traversable;

class SpreadsheetFileReader implements ReaderInterface
{
    private array $header = [];
    private int $recordsCount = 0;
    private Iterator $recordsIterator;

    /**
     * @throws ReaderException
     */
    public function __construct(string $filepath)
    {
        try {
            $spreadsheet = PhpSpreadsheet\IOFactory::createReaderForFile($filepath)
                ->setReadDataOnly(true)
                ->load($filepath);

            $sheet = $spreadsheet->getSheet(0);

            /** @var PhpSpreadsheet\Cell\Cell $cell */
            foreach ($sheet->getRowIterator()->current()->getCellIterator() as $cell) {
                $this->header[] = $cell->getValue();
            }

            $this->recordsIterator = $sheet->getRowIterator(2);
            $this->recordsCount = iterator_count($this->recordsIterator);
        } catch (PhpSpreadsheet\Exception $e) {
            throw new ReaderException($e->getMessage(), previous: $e);
        }
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getRecordsCount(): int
    {
        return $this->recordsCount;
    }

    public function getRecordsIterator(): Iterator
    {
        return new class($this->recordsIterator, $this->header) extends \IteratorIterator {
            public function __construct(Traversable $iterator, public array $header)
            {
                parent::__construct($iterator);
            }

            public function current(): array
            {
                /** @var PhpSpreadsheet\Worksheet\Row $row */
                $row = $this->getInnerIterator()->current();

                $record = [];
                /** @var PhpSpreadsheet\Cell\Cell $cell */
                foreach ($row->getCellIterator() as $cell) {
                    $record[$this->header[$cell->getXfIndex()]] = $cell->getValue();
                }
                return $record;
            }
        };
    }
}
