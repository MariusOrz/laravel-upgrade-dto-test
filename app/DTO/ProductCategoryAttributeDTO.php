<?php

declare(strict_types=1);

namespace App\DTO;

use App\Attributes\HandleWith;
use App\Base\BaseDTO;
use App\Enums\UnitTypeEnum;
use App\Handler\IsRequiredHandler;

class ProductCategoryAttributeDTO extends BaseDTO
{
    public int $id;

    public string $name;
    public ?UnitTypeEnum $unitType;

    #[HandleWith(IsRequiredHandler::class)]
    public ?bool $isRequired = false;

    public ?bool $isActive = true;
}
