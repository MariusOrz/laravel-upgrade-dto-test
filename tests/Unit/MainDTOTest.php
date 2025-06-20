<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\ProductDTO;
use Carbon\Carbon;
use Error;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('mainDTO')]
class MainDTOTest extends TestCase
{
    #[DataProvider('toArrayDataProvider')]
    public function testToArray(array $data, array $expected): void
    {
        $this->assertSame($expected, ProductDTO::from($data)->toArray());
    }

    #[DataProvider('toArrayDataProvider')]
    public function testToJson(array $data, array $expected): void
    {
        $expected = json_encode($expected, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertSame($expected, ProductDTO::from($data)->toJson());
    }

    #[DataProvider('onlyFilledToArrayDataProvider')]
    public function testOnlyFilledToArray(array $data, array $expected): void
    {
        $this->assertSame($expected, ProductDTO::from($data)->onlyFilled()->toArray());
    }

    #[DataProvider('onlyFilledToArrayDataProvider')]
    public function testOnlyFilledToJson(array $data, array $expected): void
    {
        $expected = json_encode($expected, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->assertSame($expected, ProductDTO::from($data)->onlyFilled()->toJson());
    }

    public function testHasOnly(): void
    {
        $dto = ProductDTO::from([
            'id' => 1,
            'name' => $this->faker()->word(),
            'description' => $this->faker()->word(),
            'imageUrl' => $this->faker()->imageUrl(),
            'createdAt' => '2025-01-18 15:15:30',
            'categories' => [],
        ]);

        $this->assertFalse($dto->hasOnly());

        $dto->onlyFilled();

        $this->assertTrue($dto->hasOnly());
    }

    public function testOnlyToArrayFirst(): void
    {
        $dto = ProductDTO::from([
            'id' => 1,
            'name' => $name = $this->faker()->word(),
            'description' => $this->faker()->word(),
            'imageUrl' => $this->faker()->imageUrl(),
            'createdAt' => '2025-01-18 15:15:30',
            'categories' => [
                [
                    'id' => 1,
                    'name' => 'Category 1',
                    'description' => 'Description 1',
                    'attributes' => [
                        [
                            'id' => 1,
                            'name' => 'Attribute 1',
                            'unitType' => 'kg',
                            'isRequired' => true,
                            'isActive' => true,
                        ],
                        [
                            'id' => 2,
                            'name' => 'Attribute 2',
                            'unitType' => null,
                            'isRequired' => false,
                        ],
                    ],
                ],
                ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                [
                    'id' => 3,
                    'name' => 'Category 3',
                    'description' => 'Description 3',
                    'attributes' => [
                        [
                            'id' => 3,
                            'name' => 'Attribute 3',
                            'unitType' => 'l',
                            'isRequired' => true,
                            'isActive' => false,
                        ],
                    ],
                ],
                ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
            ],
        ]);

        $expected = [
            'name' => $name,
            'categories' => [
                [
                    'name' => 'Category 1',
                    'description' => 'Description 1',
                ],
                ['name' => 'Category 2', 'description' => null],
                [
                    'name' => 'Category 3',
                    'description' => 'Description 3',
                ],
                ['name' => 'Category 3', 'description' => 'Description 3'],
            ],
        ];

        $this->assertSame($expected, $dto->only('name', 'categories.name', 'categories.description')->toArray());
    }

    public function testOnlyToArraySecond(): void
    {
        $dto = ProductDTO::from([
            'id' => 1,
            'name' => $name = $this->faker()->word(),
            'description' => $this->faker()->word(),
            'imageUrl' => $this->faker()->imageUrl(),
            'createdAt' => '2025-01-18 15:15:30',
            'categories' => [
                [
                    'id' => 1,
                    'name' => 'Category 1',
                    'description' => 'Description 1',
                    'attributes' => [
                        [
                            'id' => 1,
                            'name' => 'Attribute 1',
                            'unitType' => 'kg',
                            'isRequired' => true,
                            'isActive' => true,
                        ],
                        [
                            'id' => 2,
                            'name' => 'Attribute 2',
                            'unitType' => null,
                            'isRequired' => false,
                        ],
                    ],
                ],
                ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                [
                    'id' => 3,
                    'name' => 'Category 3',
                    'description' => 'Description 3',
                    'attributes' => [
                        [
                            'id' => 3,
                            'name' => 'Attribute 3',
                            'unitType' => 'l',
                            'isRequired' => true,
                            'isActive' => false,
                        ],
                    ],
                ],
                ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
            ],
        ]);

        $expected = [
            'id' => 1,
            'name' => $name,
            'categories' => [
                [
                    'name' => 'Category 1',
                    'attributes' => [
                        [
                            'id' => 1,
                            'unitType' => 'kg',
                        ],
                        [
                            'id' => 2,
                            'unitType' => null,
                        ],
                    ],
                ],
                ['name' => 'Category 2', 'attributes' => []],
                [
                    'name' => 'Category 3',
                    'attributes' => [
                        [
                            'id' => 3,
                            'unitType' => 'l',
                        ],
                    ],
                ],
                ['name' => 'Category 3', 'attributes' => []],
            ],
        ];

        $this->assertSame(
            $expected,
            $dto
                ->only('id', 'name', 'categories.name', 'categories.attributes.id', 'categories.attributes.unitType')
                ->toArray()
        );
    }

    public function testToArrayWhenException(): void
    {
        $dto = ProductDTO::from(['name' => $this->faker()->word()]);

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Typed property App\DTO\ProductDTO::$id must not be accessed before initialization');

        $dto->toArray();
    }

    public static function toArrayDataProvider(): array
    {
        $faker = self::createFaker();

        return [
            'payload includes properties with null values and empty arrays' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'description' => $description = $faker->word(),
                    'imageUrl' => $imageUrl = $faker->imageUrl(),
                    'createdAt' => '2025-01-18 15:15:30',
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'unitType' => null,
                                    'isRequired' => false,
                                ],
                            ],
                        ],
                        ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => 'l',
                                    'isRequired' => true,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
                    ],
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'description' => $description,
                    'imageUrl' => $imageUrl,
                    'createdAt' => '2025-01-18T15:15:30+00:00',
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => false,
                                    'isActive' => true,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'unitType' => null,
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => 'l',
                                    'isRequired' => false,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3', 'attributes' => []],
                    ],
                ],
            ],
            'payload includes properties with null values and empty arrays, additionally skip properties' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'description' => $description = $faker->word(),
                    'imageUrl' => $imageUrl = $faker->imageUrl(),
                    'createdAt' => $createdAt = Carbon::parse('2025-01-18 15:15:30'),
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => false,
                                    'isActive' => false,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'isRequired' => true,
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => null,
                                ],
                            ],
                        ],
                        [
                            'id' => 2,
                            'name' => 'Category 2',
                            'attributes' => [
                                [
                                    'id' => 4,
                                    'name' => 'Attribute 4',
                                    'unitType' => null,
                                    'isRequired' => false,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 5,
                                    'name' => 'Attribute 5',
                                    'unitType' => 'l',
                                    'isRequired' => false,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
                    ],
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'description' => $description,
                    'imageUrl' => $imageUrl,
                    'createdAt' => $createdAt->toIso8601String(),
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => true,
                                    'isActive' => false,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'unitType' => null,
                                    'isRequired' => false,
                                    'isActive' => true,
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => null,
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        [
                            'id' => 2,
                            'name' => 'Category 2',
                            'description' => null,
                            'attributes' => [
                                [
                                    'id' => 4,
                                    'name' => 'Attribute 4',
                                    'unitType' => null,
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 5,
                                    'name' => 'Attribute 5',
                                    'unitType' => 'l',
                                    'isRequired' => true,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3', 'attributes' => []],
                    ],
                ],
            ],
            'payload without passing categories property' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'createdAt' => $createdAt = Carbon::parse('2025-01-18 15:15:30'),
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'description' => null,
                    'imageUrl' => null,
                    'createdAt' => $createdAt->toIso8601String(),
                    'categories' => [],
                ],
            ],
            'payload when passing categories as empty array' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'createdAt' => $createdAt = Carbon::parse('2025-01-18 15:15:30'),
                    'categories' => [],
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'description' => null,
                    'imageUrl' => null,
                    'createdAt' => $createdAt->toIso8601String(),
                    'categories' => [],
                ],
            ],
        ];
    }

    public static function onlyFilledToArrayDataProvider(): array
    {
        $faker = self::createFaker();

        return [
            'payload includes properties with null values and empty arrays' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'description' => $description = $faker->word(),
                    'imageUrl' => $imageUrl = $faker->imageUrl(),
                    'createdAt' => '2025-01-18 15:15:30',
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'unitType' => null,
                                    'isRequired' => false,
                                ],
                            ],
                        ],
                        ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => 'l',
                                    'isRequired' => true,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
                    ],
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'description' => $description,
                    'imageUrl' => $imageUrl,
                    'createdAt' => '2025-01-18T15:15:30+00:00',
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => false,
                                    'isActive' => true,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'unitType' => null,
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        ['id' => 2, 'name' => 'Category 2', 'description' => null, 'attributes' => []],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => 'l',
                                    'isRequired' => false,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3', 'attributes' => []],
                    ],
                ],
            ],
            'payload includes properties with null values and empty arrays, additionally skip properties' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'description' => $description = $faker->word(),
                    'imageUrl' => null,
                    'createdAt' => $createdAt = Carbon::parse('2025-01-18 15:15:30'),
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => false,
                                    'isActive' => false,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'isRequired' => true,
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => null,
                                ],
                            ],
                        ],
                        [
                            'id' => 2,
                            'name' => 'Category 2',
                            'attributes' => [
                                [
                                    'id' => 4,
                                    'name' => 'Attribute 4',
                                    'unitType' => null,
                                    'isRequired' => false,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 5,
                                    'name' => 'Attribute 5',
                                    'unitType' => 'l',
                                    'isRequired' => false,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3'],
                    ],
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'description' => $description,
                    'createdAt' => $createdAt->toIso8601String(),
                    'categories' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'description' => 'Description 1',
                            'attributes' => [
                                [
                                    'id' => 1,
                                    'name' => 'Attribute 1',
                                    'unitType' => 'kg',
                                    'isRequired' => true,
                                    'isActive' => false,
                                ],
                                [
                                    'id' => 2,
                                    'name' => 'Attribute 2',
                                    'unitType' => null,
                                    'isRequired' => false,
                                    'isActive' => true,
                                ],
                                [
                                    'id' => 3,
                                    'name' => 'Attribute 3',
                                    'unitType' => null,
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        [
                            'id' => 2,
                            'name' => 'Category 2',
                            'description' => null,
                            'attributes' => [
                                [
                                    'id' => 4,
                                    'name' => 'Attribute 4',
                                    'unitType' => null,
                                    'isRequired' => true,
                                    'isActive' => true,
                                ],
                            ],
                        ],
                        [
                            'id' => 3,
                            'name' => 'Category 3',
                            'description' => 'Description 3',
                            'attributes' => [
                                [
                                    'id' => 5,
                                    'name' => 'Attribute 5',
                                    'unitType' => 'l',
                                    'isRequired' => true,
                                    'isActive' => false,
                                ],
                            ],
                        ],
                        ['id' => 4, 'name' => 'Category 3', 'description' => 'Description 3', 'attributes' => []],
                    ],
                ],
            ],
            'payload without passing categories property' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'createdAt' => $createdAt = Carbon::parse('2025-01-18 15:15:30'),
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'createdAt' => $createdAt->toIso8601String(),
                ],
            ],
            'payload when passing categories as empty array' => [
                'data' => [
                    'id' => 1,
                    'name' => $name = $faker->word(),
                    'createdAt' => $createdAt = Carbon::parse('2025-01-18 15:15:30'),
                    'categories' => [],
                ],
                'expected' => [
                    'id' => 1,
                    'name' => $name,
                    'createdAt' => $createdAt->toIso8601String(),
                ],
            ],
        ];
    }

    public static function createFaker(): Generator
    {
        return Factory::create();
    }

    protected function faker(): Generator
    {
        return Factory::create();
    }
}
