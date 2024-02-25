<?php

namespace Armezit\Lunar\VirtualProduct\Actions\CodePool;

use Armezit\Lunar\VirtualProduct\Exceptions;
use Armezit\Lunar\VirtualProduct\Models\CodePoolArchive;
use Armezit\Lunar\VirtualProduct\Models\CodePoolItem;
use Illuminate\Support\Facades\DB;
use Lunar\Models\OrderLine;
use Throwable;

/**
 * Pull available code pool items for a given order line
 */
class PullOrderLineItem
{
    /**
     * Execute the action
     *
     * @throws Exceptions\CodePool\OutOfStockException
     * @throws Exceptions\CodePool\PullOrderLineItemException
     */
    public function execute(OrderLine $orderLine): void
    {
        $itemIds = CodePoolItem::forPurchasable($orderLine->purchasable_id)
            ->limit($orderLine->quantity)
            ->select('id')
            ->get();

        if (count($itemIds) < $orderLine->quantity) {
            throw new Exceptions\CodePool\OutOfStockException($orderLine, count($itemIds));
        }

        DB::beginTransaction();
        try {
            $query = CodePoolItem::query()->whereIn('id', $itemIds);

            // pull/archive items
            $archiveData = $query
                ->get()
                ->map(function (CodePoolItem $item) use ($orderLine) {
                    $record = $item->toArray();
                    $record['order_line_id'] = $orderLine->id;
                    $record['data'] = json_encode($item->data);
                    unset($record['id']);

                    return $record;
                })
                ->toArray();

            CodePoolArchive::insert($archiveData);

            // delete pulled items
            $query->delete();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exceptions\CodePool\PullOrderLineItemException(previous: $e);
        }
    }
}
