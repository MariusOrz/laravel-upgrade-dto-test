<?php

declare(strict_types=1);

namespace App\Enums;

enum UnitTypeEnum: string
{
    case unit = 'unit';
    case l = 'l';
    case ml = 'ml';
    case kg = 'kg';
    case g = 'g';
}
