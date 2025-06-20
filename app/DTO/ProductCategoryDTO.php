<?php

declare(strict_types=1);

namespace App\DTO;

use App\Base\BaseDTO;
use App\Casters\ArrayCaster;
use Spatie\LaravelData\Attributes\WithCast;

class ProductCategoryDTO extends BaseDTO
{
    public int $id;

    public string $name;

    public ?string $description;

    #[WithCast(ArrayCaster::class, ProductCategoryAttributeDTO::class)]
    public array $attributes = [];
}
