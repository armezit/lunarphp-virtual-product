<?php

namespace Armezit\Lunar\VirtualProduct\Commands;

use Illuminate\Console\Command;

class ListVirtualProducts extends Command
{
    public $signature = 'lunar:virtual-product:list';

    public $description = 'List all defined virtual products';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
