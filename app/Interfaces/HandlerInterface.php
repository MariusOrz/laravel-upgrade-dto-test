<?php

declare(strict_types=1);

namespace App\Interfaces;

interface HandlerInterface
{
    public function handle(): mixed;
}
