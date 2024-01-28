<?php

namespace Armezit\Lunar\VirtualProduct\Exceptions\CodePool;

use Armezit\Lunar\VirtualProduct\Exceptions\CodePoolException;
use Lunar\Models\OrderLine;

/**
 * There is not enough available stock for a virtual item
 */
class OutOfStockException extends CodePoolException
{
    /**
     * @param OrderLine $orderLine
     * @param int $available Available code-pool items count
     * @param \Throwable|null $previous
     */
    public function __construct(
        public OrderLine $orderLine,
        public int       $available,
        ?\Throwable      $previous = null
    )
    {
        parent::__construct('virtual product is out of stock', previous: $previous);
    }
}
