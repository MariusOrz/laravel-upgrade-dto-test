<?php

declare(strict_types=1);

namespace App\Handler;

use App\DTO\ProductCategoryAttributeDTO;
use App\Interfaces\HandlerInterface;

class IsRequiredHandler implements HandlerInterface
{
    public function __construct(public ProductCategoryAttributeDTO $dto, public array $payload)
    {
    }

    public function handle(): mixed
    {
        return !$this->dto->isRequired;
    }
}
