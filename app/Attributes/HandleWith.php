<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HandleWith
{
    public function __construct(public string $handlerClass)
    {
    }
}
