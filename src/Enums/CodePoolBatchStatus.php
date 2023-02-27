<?php

namespace Armezit\Lunar\VirtualProduct\Enums;

enum CodePoolBatchStatus: string
{
    case Running = 'running';
    case Failed = 'failed';
    case Completed = 'completed';

    public static function labels(): array
    {
        return [
            self::Running->value => self::t(self::Running),
            self::Failed->value => self::t(self::Failed),
            self::Completed->value => self::t(self::Completed),
        ];
    }

    private static function t(self $v)
    {
        return __('lunarphp-virtual-product::code-pool.batch_status.'.$v->value);
    }
}
