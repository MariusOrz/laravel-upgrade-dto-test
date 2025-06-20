<?php

declare(strict_types=1);

namespace App\DTO;

use App\Base\BaseDTO;
use App\Casters\ArrayCaster;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;

class ProductDTO extends BaseDTO
{
    public int $id;

    public string $name;
    public ?string $description;
    public ?string $imageUrl;

    public Carbon $createdAt;

    #[WithCast(ArrayCaster::class, ProductCategoryDTO::class)]
    public array $categories = [];
}
