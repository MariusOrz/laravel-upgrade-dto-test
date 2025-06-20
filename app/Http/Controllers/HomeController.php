<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ProductDTO;

class HomeController extends Controller
{
    public function index()
    {
        $dto = ProductDTO::from([
            'id' => 1,
            'name' => 'Test Product',
//            'description' => 'This is a test product.',
            'imageUrl' => 'http://example.com/image.jpg',
            'createdAt' => '2023-10-01T12:00:00Z',
            'categories' => [
                ['id' => 1, 'name' => 'Category 1', 'description' => 'Description 1', 'attributes' => [
                    ['id' => 1, 'name' => 'Attribute 1', 'unitType' => 'kg', 'isRequired' => true, 'isActive' => true],
                    ['id' => 2, 'name' => 'Attribute 2', 'unitType' => null, 'isRequired' => false],
                ]],
                ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                ['id' => 3, 'name' => 'Category 3', 'description' => 'Description 3', 'attributes' => [
                    ['id' => 3, 'name' => 'Attribute 3', 'unitType' => 'l', 'isRequired' => true, 'isActive' => false],
                ]],
                ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
            ],
        ]);

        dd(
//            json_decode($dto->onlyFilled()->toJson(), true),
            $dto->toArray()
        );
    }
}
