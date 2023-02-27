<?php

namespace Armezit\Lunar\VirtualProduct\Exceptions;

class FieldValidationException extends VirtualProductException
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
