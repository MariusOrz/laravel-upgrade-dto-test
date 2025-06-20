<?php

declare(strict_types=1);

namespace App\Overrides;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\PartialTrees;
use Spatie\LaravelData\Transformers\DataTransformer as DataTransformerLaravelData;

class DataTransformer extends DataTransformerLaravelData
{
    protected function resolvePropertyValue(DataProperty $property, mixed $value, PartialTrees $trees): mixed
    {
        if (is_array($value) && array_is_list($value)) {
            return array_map(function (mixed $item) use ($property, $trees) {
                if ($item instanceof Data) {
                    return $item->withPartialTrees($trees)->toArray();
                }

                return $this->resolvePropertyValue($property, $item, $trees);
            }, $value);
        }

        return parent::resolvePropertyValue($property, $value, $trees);
    }
}
