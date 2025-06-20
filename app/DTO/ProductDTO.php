<?php

declare(strict_types=1);

namespace App\DTO;

use App\Attributes\MapInputName;
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

    #[MapInputName('product_code')]
    public ?string $code;

    public Carbon $createdAt;

    #[WithCast(ArrayCaster::class, ProductCategoryDTO::class)]
    public array $categories = [];
}
