<?php

namespace Armezit\Lunar\VirtualProduct\Enums;

enum CodePoolFieldType: string
{
    case Raw = 'raw';
    case Integer = 'integer';
    case Float = 'float';
    case Email = 'email';
    case Url = 'url';

    public static function labels(): array
    {
        return [
            self::Raw->value => self::t(self::Raw),
            self::Integer->value => self::t(self::Integer),
            self::Float->value => self::t(self::Float),
            self::Email->value => self::t(self::Email),
            self::Url->value => self::t(self::Url),
        ];
    }

    private static function t(self $v)
    {
        return __('lunarphp-virtual-product::code-pool.field_type.'.$v->value);
    }
}
