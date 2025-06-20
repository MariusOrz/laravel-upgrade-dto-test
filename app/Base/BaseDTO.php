<?php

declare(strict_types=1);

namespace App\Base;

use App\Attributes\HandleWith;
use App\DataPipes\DefaultValuesDataPipe;
use App\DataPipes\MapPropertiesDataPipe;
use App\Interfaces\HandlerInterface;
use App\Overrides\DataTransformer;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataPipeline;
use Spatie\LaravelData\DataPipes\AuthorizedDataPipe;
use Spatie\LaravelData\DataPipes\CastPropertiesDataPipe;
use Spatie\LaravelData\DataPipes\ValidatePropertiesDataPipe;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;

class BaseDTO extends Data
{
    protected const NOT_PROCESSABLE_OBJECTS = [Carbon::class];

    public static function pipeline(): DataPipeline
    {
        return DataPipeline::create()
            ->into(static::class)
            ->through(AuthorizedDataPipe::class)
            ->through(MapPropertiesDataPipe::class)
            ->through(ValidatePropertiesDataPipe::class)
            ->through(DefaultValuesDataPipe::class)
            ->through(CastPropertiesDataPipe::class);
    }

    public static function from(...$payloads): static
    {
        $instance = parent::from(...$payloads);

        self::handleArguments($instance, $payloads);

        $instance->afterPipelines();

        return $instance;
    }

    public function onlyFilled(): static
    {
        $this->onlyFilledArrayWalk($this);

        // Set the _only property to an empty array if no properties are filled, we'll know that onlyFilled was called
        if (!$this->hasOnly()) {
            $this->_only = ['keyNotDefinedAnywhere' => true];
        }

        return $this;
    }

    public function toJson($options = 100): string
    {
        return parent::toJson($options);
    }

    public function hasOnly(): bool
    {
        return count($this->_only) > 0;
    }

    protected function afterPipelines(): void
    {
    }

    protected function onlyFilledArrayWalk(mixed $data, ?string $prependKey = null): void
    {
        if (!isset($data)) {
            return;
        }

        if (is_object($data) && !$this->isUnprocessableObject($data)) {
            $this->handleObjectInArrayWalk($data, $prependKey);

            return;
        }

        if (is_array($data)) {
            $this->handleArrayInArrayWalk($data, $prependKey);

            return;
        }

        $this->only($prependKey);
    }

    protected function handleObjectInArrayWalk(object $data, ?string $prependKey = null): void
    {
        $properties = (new ReflectionClass($data))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $fullKey = $prependKey ? "$prependKey." . $property->getName() : $property->getName();

            if (!isset($data->{$property->getName()})) {
                continue;
            }

            $value = $property->getValue($data);

            $this->onlyFilledArrayWalk($value, $fullKey);
        }
    }

    protected function handleArrayInArrayWalk(array $data, ?string $prependKey = null): void
    {
        $isShouldAppend = $prependKey && !array_is_list($data);

        foreach ($data as $key => $value) {
            $arrayKey = $isShouldAppend ? "$prependKey.$key" : $prependKey;

            $this->onlyFilledArrayWalk($value, $arrayKey);
        }
    }

    /** @override */
    public function transform(
        bool $transformValues = true,
        WrapExecutionType $wrapExecutionType = WrapExecutionType::Disabled,
        bool $mapPropertyNames = true,
    ): array {
        return (new DataTransformer($transformValues, $wrapExecutionType, $mapPropertyNames))->transform($this);
    }

    protected static function handleArguments(BaseData $instance, array $payloads): void
    {
        $class = new ReflectionClass($instance);

        $properties = array_filter(
            $class->getProperties(),
            static fn(ReflectionProperty $property) =>
                property_exists($instance, $property->getName())
                && $property->getAttributes(HandleWith::class)
        );

        foreach ($properties as $property) {
            $attribute = $property->getAttributes(HandleWith::class)[0];

            $handlerClass = $attribute->getArguments()[0];

            $handlerInstance = new $handlerClass($instance, $payloads);

            if (!($handlerInstance instanceof HandlerInterface)) {
                continue;
            }

            $instance->{$property->getName()} = $handlerInstance->handle();
        }
    }

    protected function isUnprocessableObject(object $object): bool
    {
        $isInstanceOfAny = array_map(static fn($class) => $object instanceof $class, self::NOT_PROCESSABLE_OBJECTS);

        return in_array(true, $isInstanceOfAny, true);
    }
}
