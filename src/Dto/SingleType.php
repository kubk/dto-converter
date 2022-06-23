<?php

declare(strict_types=1);

namespace Riverwaysoft\DtoConverter\Dto;

class SingleType implements \JsonSerializable
{
    public function __construct(
        private string $name,
    ) {
    }

    public static function null(): self
    {
        return new self(name: 'null');
    }

    public function isNull(): bool
    {
        return $this->name === 'null';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
        ];
    }
}
