<?php

namespace Armezit\Lunar\VirtualProduct\Exceptions;

class FieldValidationException extends VirtualProductException
{
    public function __construct($field = "") {
        parent::__construct($message);
    }
}
