<?php

declare(strict_types=1);

namespace App\Tests\OutputGenerator;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Riverwaysoft\PhpConverter\Dto\DtoEnumProperty;
use Riverwaysoft\PhpConverter\Dto\DtoType;
use Riverwaysoft\PhpConverter\Dto\ExpressionType;
use Riverwaysoft\PhpConverter\OutputGenerator\Dart\DartEnumValidator;

class DartEnumValidatorTest extends TestCase
{
    public function testValidationForDart(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Test requires PHP 8.1');
        }

        $dartEnumValidator = new DartEnumValidator();

        $enumValid = new DtoType(
            name: 'ValidEnum',
            expressionType: ExpressionType::enum(),
            properties: [
                new DtoEnumProperty(
                    name: 'First',
                    value: 'First'
                ),
                new DtoEnumProperty(
                    name: 'Second',
                    value: 'Second'
                ),
            ]
        );

        $dartEnumValidator->assertIsValidEnumForDart($enumValid);
        $this->expectNotToPerformAssertions();
    }

    #[DataProvider('provideInvalidData')]
    public function testInvalidEnumForDart(DtoType $enumInvalid, string $message): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Test requires PHP 8.1');
        }

        $dartEnumValidator = new DartEnumValidator();

        $this->expectExceptionMessage($message);
        $dartEnumValidator->assertIsValidEnumForDart($enumInvalid);
    }

    public static function provideInvalidData(): Generator
    {
        yield [
            new DtoType(
                name: 'InvalidEnum',
                expressionType: ExpressionType::enum(),
                properties: [
                    new DtoEnumProperty(
                        name: 'First',
                        value: 'first'
                    ),
                    new DtoEnumProperty(
                        name: 'Second',
                        value: 'second'
                    ),
                ]
            ),
            'String enum InvalidEnum should have identical keys and values to be supported by Dart. Error key "First" and value "first". Rename one of those to make sure they are equal',
        ];

        yield [
            new DtoType(
                name: 'InvalidEnum',
                expressionType: ExpressionType::enum(),
                properties: [
                    new DtoEnumProperty(
                        name: 'First',
                        value: 1,
                    ),
                    new DtoEnumProperty(
                        name: 'Second',
                        value: 2,
                    ),
                ]
            ),
            'Numeric enum InvalidEnum must start with 0 to be supported by Dart',
        ];

        yield [
            new DtoType(
                name: 'InvalidEnum',
                expressionType: ExpressionType::enum(),
                properties: [
                    new DtoEnumProperty(
                        name: 'First',
                        value: 0,
                    ),
                    new DtoEnumProperty(
                        name: 'Second',
                        value: 2,
                    ),
                ]
            ),
            'Numeric enum InvalidEnum should not have holes in the array to be supported by Dart. Missed values: 1',
        ];
    }
}
