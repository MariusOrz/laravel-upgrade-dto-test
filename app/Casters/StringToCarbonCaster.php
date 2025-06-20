<?php

declare(strict_types=1);

namespace App\Casters;

use Carbon\Carbon;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class StringToCarbonCaster implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): ?Carbon
    {
        if (!$value) {
            return null;
        }

        if (is_int($value)) {
            return Carbon::createFromTimestampMs($value);
        }

        if (!is_string($value)) {
            return null;
        }

        return Carbon::parse($value);
    }

}
