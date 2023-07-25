<?php

declare(strict_types=1);

namespace Riverwaysoft\PhpConverter\Dto;

use JsonSerializable;
use Riverwaysoft\PhpConverter\Dto\PhpType\PhpUnknownType;
use function gettype;
use function count;

class DtoType implements JsonSerializable
{
    public function __construct(
        private string $name,
        private ExpressionType $expressionType,
        /** @var DtoClassProperty[]|DtoEnumProperty[] $properties */
        private array $properties,
        /** @var PhpUnknownType[] */
        private array $generics = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExpressionType(): ExpressionType
    {
        return $this->expressionType;
    }

    /** @return DtoClassProperty[]|DtoEnumProperty[] */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function isGeneric(): bool
    {
        return count($this->generics) > 0;
    }

    public function hasGeneric(PhpUnknownType $type): bool
    {
        foreach ($this->generics as $generic) {
            if ($type->getName() === $generic->getName()) {
                return true;
            }
        }

        return false;
    }

    /** @return PhpUnknownType[] */
    public function getGenerics(): array
    {
        return $this->generics;
    }

    public function isStringEnum(): bool
    {
        if (!$this->expressionType->isAnyEnum()) {
            return false;
        }

        $isEveryPropertyString = true;

        foreach ($this->properties as $property) {
            if (gettype($property->getValue()) !== 'string') {
                $isEveryPropertyString = false;
                break;
            }
        }

        return $isEveryPropertyString;
    }

    public function isEmpty(): bool
    {
        return count($this->properties) === 0;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'expressionType' => $this->expressionType,
            'properties' => $this->properties,
            'generics' => $this->generics,
        ];
    }
}
