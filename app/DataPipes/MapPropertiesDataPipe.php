<?php

declare(strict_types=1);

namespace App\DataPipes;

use App\Attributes\MapInputName;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataPipes\DataPipe;
use Spatie\LaravelData\Support\DataClass;
use Spatie\LaravelData\Support\DataProperty;

class MapPropertiesDataPipe implements DataPipe
{
    public function handle(mixed $payload, DataClass $class, Collection $properties): Collection
    {
        $filteredProperties = $class->properties->filter(
            static fn(DataProperty $dataProperty) => $dataProperty->attributes
                ->first(fn(mixed $item) => $item instanceof MapInputName)
        );

        /** @var DataProperty $property */
        foreach ($filteredProperties as $property) {
            /** @var MapInputName $mapFromAttribute */
            $mapFromAttribute = $property->attributes->first(fn(mixed $item) => $item instanceof MapInputName);

            if (is_string($keys = $mapFromAttribute->keys)) {
                if (is_null($value = data_get($properties, $keys))) {
                    continue;
                }

                $properties[$property->name] = $value;

                continue;
            }

            if (is_array($keys)) {
                foreach ($keys as $key) {
                    if (is_null($value = data_get($properties, $key))) {
                        continue;
                    }

                    $properties[$property->name] = $value;

                    break;
                }
            }
        }

        return $properties;
    }
}
