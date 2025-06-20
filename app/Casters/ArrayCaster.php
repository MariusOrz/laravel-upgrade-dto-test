<?php

declare(strict_types=1);

namespace App\Casters;

use Illuminate\Support\Arr;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class ArrayCaster implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): array
    {
        $data = [];

        if (!isset($value)) {
            return $data;
        }

        if (
            !($attribute = $property->attributes->firstOrFail(
                static fn(mixed $attribute) => $attribute instanceof WithCast
            ))
        ) {
            return $data;
        }

        $dtoClass = Arr::get($attribute->arguments, 0);

        foreach ($value as $key => $item) {
            $data[$key] = $dtoClass::from($item);
        }

        return $data;
    }
}
