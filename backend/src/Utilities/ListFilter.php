<?php

declare(strict_types=1);

namespace App\Utilities;

use BackedEnum;

/**
 * A filterable list field with its allowed values
 */
final readonly class ListFilter
{
    /**
     * @param list<string> $allowedValues
     */
    public function __construct(
        public string $field,
        public array $allowedValues
    ) {
    }

    /**
     * @param class-string<BackedEnum> $enum
     */
    public static function fromEnum(string $field, string $enum): self
    {
        $values = [];
        foreach ($enum::cases() as $case) {
            $values[] = (string) $case->value;
        }

        return new self($field, $values);
    }
}
