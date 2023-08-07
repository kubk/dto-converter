<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\SnapshotComparator\DartSnapshotComparator;
use PHPUnit\Framework\TestCase;
use Riverwaysoft\PhpConverter\Ast\Converter;
use Riverwaysoft\PhpConverter\Ast\DtoVisitor;
use Riverwaysoft\PhpConverter\OutputGenerator\Dart\DartClassFactoryGenerator;
use Riverwaysoft\PhpConverter\OutputGenerator\Dart\DartEquitableGenerator;
use Riverwaysoft\PhpConverter\OutputGenerator\Dart\DartOutputGenerator;
use Riverwaysoft\PhpConverter\OutputGenerator\Dart\DartImportGenerator;
use Riverwaysoft\PhpConverter\OutputGenerator\Dart\DartTypeResolver;
use Riverwaysoft\PhpConverter\OutputGenerator\UnknownTypeResolver\ClassNameTypeResolver;
use Riverwaysoft\PhpConverter\OutputGenerator\UnknownTypeResolver\DateTimeTypeResolver;
use Riverwaysoft\PhpConverter\OutputWriter\EntityPerClassOutputWriter\DtoTypeDependencyCalculator;
use Riverwaysoft\PhpConverter\OutputWriter\EntityPerClassOutputWriter\EntityPerClassOutputWriter;
use Riverwaysoft\PhpConverter\OutputWriter\EntityPerClassOutputWriter\SnakeCaseFileNameGenerator;
use Riverwaysoft\PhpConverter\OutputWriter\OutputProcessor\OutputFilesProcessor;
use Riverwaysoft\PhpConverter\OutputWriter\OutputProcessor\PrependAutogeneratedNoticeFileProcessor;
use Riverwaysoft\PhpConverter\OutputWriter\SingleFileOutputWriter\SingleFileOutputWriter;
use Spatie\Snapshots\MatchesSnapshots;

class DartGeneratorTest extends TestCase
{
    use MatchesSnapshots;

    public function testDart(): void
    {
        $codeDart = <<<'CODE'
<?php

use MyCLabs\Enum\Enum;

final class ColorEnum extends Enum
{
    private const RED = 0;
    private const GREEN = 1;
    private const BLUE = 2;
}

class Category
{
    public string $id;
    public string $title;
    public int $rating;
    /** @var Recipe[] */
    public array $recipes;
}

class Recipe
{
    public string $id;
    public ?string $imageUrl;
    public string|null $url;
    public bool $isCooked;
    public float $weight;
}

class User
{
    public string $id;
    public ?User $bestFriend;
    /** @var User[] */
    public array $friends;
    public ColorEnum $themeColor;
    public string|int $stringOrInteger;
}

/**
* @template T
 */
class Response {
    /**
    * @param T $data
    */
    public function __construct(
        public $data,
    ) {}
}
CODE;

        $normalized = (new Converter([new DtoVisitor()]))->convert([$codeDart]);

        $results = (new DartOutputGenerator(
            outputWriter: new SingleFileOutputWriter('generated.dart'),
            typeResolver: new DartTypeResolver([new ClassNameTypeResolver()]),
            outputFilesProcessor: new OutputFilesProcessor([
                new PrependAutogeneratedNoticeFileProcessor(),
            ])
        ))->generate($normalized);

        $this->assertCount(1, $results);
        $this->assertMatchesSnapshot($results[0]->getContent(), new DartSnapshotComparator());
    }

    public function testDartWithEquitableAndFactory(): void
    {
        $codeDart = <<<'CODE'
<?php

use MyCLabs\Enum\Enum;

final class ColorEnum extends Enum
{
    private const RED = 0;
    private const GREEN = 1;
    private const BLUE = 2;
}

class SomeEmptyClas {

}

class Category
{
    public string $id;
    public string $title;
    public int $rating;
    /** @var Recipe[] */
    public array $recipes;
}

class Recipe
{
    public string $id;
    public ?string $imageUrl;
    public string|null $url;
    public bool $isCooked;
    public float $weight;
}

class User
{
    public string $id;
    public ?User $bestFriend;
    public ?Recipe $favoriteRecipe;
    public Recipe $recipeRequired;
    /** @var User[] */
    public array $friendsRequired;
    /** @var User[]|null */
    public array|null $friendsOptional;
    public ColorEnum $themeColor;
    /** @var ColorEnum[] */
    public array $colors;
    public DayOfTheWeekEnumBackedInt $enumInt;
    public DayOfTheWeekEnumBackedString $enumString;
}

enum DayOfTheWeekEnumBackedInt: int
{
    case NONE = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;
}

enum DayOfTheWeekEnumBackedString: string
{
    case MONDAY = 'MONDAY';
    case TUESDAY = 'TUESDAY';
    case WEDNESDAY = 'WEDNESDAY';
    case THURSDAY = 'THURSDAY';
    case FRIDAY='FRIDAY';
    case SATURDAY='SATURDAY';
    case SUNDAY = 'SUNDAY';
}

#[Dto]
class UserCreateInput
{
    public ?DateTimeImmutable $promotedAt;
    public string $name;
}
CODE;

        $normalized = (new Converter([new DtoVisitor()]))->convert([$codeDart]);

        $results = (new DartOutputGenerator(
            new SingleFileOutputWriter('generated.dart'),
            new DartTypeResolver([
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
            ]),
            new OutputFilesProcessor([
                new PrependAutogeneratedNoticeFileProcessor(
                    "// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!\nimport 'package:equatable/equatable.dart';\n\n",
                ),
            ]),
            new DartClassFactoryGenerator('/Input$/'),
            new DartEquitableGenerator('/Input$/'),
        ))->generate($normalized);

        $this->assertCount(1, $results);
        $this->assertMatchesSnapshot($results[0]->getContent(), new DartSnapshotComparator());
    }

    public function testEntityPerClassOutputWriterDart(): void
    {
        $codeNestedDto = <<<'CODE'
<?php

class UserCreate {
    public string $id;
    public ?Profile $profile;
}

class FullName {
    public string $firstName;
    public string $lastName;
}

class Profile {
    public FullName|null|string $name;
    public int $age;
}
CODE;
        $normalized = (new Converter([new DtoVisitor()]))->convert([$codeNestedDto]);

        $fileNameGenerator = new SnakeCaseFileNameGenerator('.dart');
        $dartGenerator = new DartOutputGenerator(
            new EntityPerClassOutputWriter(
                $fileNameGenerator,
                new DartImportGenerator(
                    $fileNameGenerator,
                    new DtoTypeDependencyCalculator()
                )
            ),
            new DartTypeResolver([
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
            ]),
            new OutputFilesProcessor([
                new PrependAutogeneratedNoticeFileProcessor(),
            ])
        );
        $results = $dartGenerator->generate($normalized);

        $this->assertCount(3, $results);
        $this->assertMatchesSnapshot($results);
    }
}
