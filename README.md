# php-converter [![Latest Version on Packagist](https://img.shields.io/packagist/v/riverwaysoft/php-converter.svg)](https://packagist.org/packages/riverwaysoft/php-converter) [![Tests](https://github.com/riverwaysoft/php-converter/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/riverwaysoft/php-converter/actions/workflows/php.yml) [![PHPStan](https://github.com/riverwaysoft/php-converter/actions/workflows/static_analysis.yml/badge.svg?branch=master)](https://github.com/riverwaysoft/php-converter/actions/workflows/static_analysis.yml) [![Total Downloads](https://img.shields.io/packagist/dt/riverwaysoft/php-converter.svg)](https://packagist.org/packages/riverwaysoft/php-converter)

<img width="818" alt="Screen Shot 2022-10-07 at 09 04 35" src="https://user-images.githubusercontent.com/22447849/194478818-7276da5c-bf5e-4ad2-8efd-6463c53d01d3.png">

Generates TypeScript & Dart out of your PHP DTO classes.

## Why?
Statically typed languages like TypeScript or Dart are great because they allow catching bugs without even running your code. But unless you have well-defined contracts between API and consumer apps, you have to always fix outdated typings when the API changes.
This library generates types for you so you can move faster and encounter fewer bugs.

## Requirements

PHP 8.0 or above

## Quick start

1) Installation
```bash
composer require riverwaysoft/php-converter --dev
```

2) Mark a few classes with `#[Dto]` annotation to convert them into TypeScript or Dart
```php
use Riverwaysoft\PhpConverter\ClassFilter\Dto;

#[Dto]
class UserOutput
{
    public string $id;
    public int $age;
    public ?UserOutput $bestFriend;
    /** @var UserOutput[] */
    public array $friends;
}
```

4) Run CLI command to generate TypeScript
```bash
vendor/bin/php-converter-ts generate --from=/path/to/project/src --to=.
```

You'll get file `generated.ts` with the following contents:

```typescript
type UserOutput = {
  id: string;
  age: number;
  bestFriend: UserOutput | null;
  friends: UserOutput[];
}
```

## Features
- Support of all PHP data types including union types, nullable types, enums (both [native PHP 8.1 enums](https://www.php.net/manual/en/language.enumerations.overview.php) and [MyCLabs enums](https://github.com/myclabs/php-enum))
- PHP DocBlock type support e.g `User[]`, `int[][]|null` 
- Nested types
- Recursive types
- Custom type resolvers (for example for `DateTimeImmutable`, etc)
- Generate a single output file or multiple files (1 type per file). An option to override the generation logic
- Flexible class filters with an option to use your own filters

## Customize
If you'd like to customize `php-converter-ts` you need to copy the generator script to your project folder:

```
cp vendor/bin/php-converter-ts bin/php-converter-ts
``` 

Now you can start customizing the php-converter by editing the executable file.

### How to customize generated output?
By default `php-converter` writes all the types into one file. You can configure it to put each type / class in a separate file with all the required imports. Here is an example how to achieve it:

```diff
+ $fileNameGenerator = new KebabCaseFileNameGenerator('.ts');

$application->add(
    new ConvertCommand(
        new Converter([
            new DtoVisitor(new PhpAttributeFilter('Dto')),
        ]),
        new TypeScriptGenerator(
-            new SingleFileOutputWriter('generated.ts'),
+            new EntityPerClassOutputWriter(
+                $fileNameGenerator,
+                new TypeScriptImportGenerator(
+                    $fileNameGenerator,
+                    new DtoTypeDependencyCalculator()
+                )
+            ),
            [
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
            ],
        ),
        new Filesystem(),
        new OutputDiffCalculator(),
        new FileSystemCodeProvider('/\.php$/'),
    )
);
```

Feel free to create your own OutputWriter.

### How to customize class filtering?
Suppose you don't want to mark each DTO individually with `#[Dto]` but want to convert all the files ending with "Dto" automatically:

```diff
$application->add(
    new ConvertCommand(
-       new Converter([
-           new DtoVisitor(new PhpAttributeFilter('Dto')),
-       ]),
+       new Converter([new DtoVisitor()]),
        new TypeScriptGenerator(
            new SingleFileOutputWriter('generated.ts'),
            [
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
            ],
        ),
        new Filesystem(),
        new OutputDiffCalculator(),
-       new FileSystemCodeProvider('/\.php$/'),
+       new FileSystemCodeProvider('/Dto\.php$/'),
    )
);
```

You can even go further and use `NegationFilter` to exclude specific files as shown in [unit tests](https://github.com/riverwaysoft/php-converter/blob/a8d5df2c03303c02bc9148bd1d7822d7fe48c5d8/tests/EndToEndTest.php#L297).

### How to write custom type resolvers?
`php-converter` takes care of converting basic PHP types like number, string and so on. But what if you have a type that isn't a DTO? For example `\DateTimeImmutable`. You can write a class that implements [UnknownTypeResolverInterface](https://github.com/riverwaysoft/php-converter/blob/2d434562c1bc73bcb6819257b31dd75c818f4ab1/src/Language/UnknownTypeResolverInterface.php). There is also a shortcut to achieve it - use [InlineTypeResolver](https://github.com/riverwaysoft/php-converter/blob/2d434562c1bc73bcb6819257b31dd75c818f4ab1/src/Language/TypeScript/InlineTypeResolver.php):

```diff
+use Riverwaysoft\PhpConverter\Dto\PhpType\PhpBaseType;

$application->add(
    new ConvertCommand(
        new Converter([
            new DtoVisitor(new PhpAttributeFilter('Dto')),
        ]),
        new TypeScriptGenerator(
            new SingleFileOutputWriter('generated.ts'),
            [
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
+               new InlineTypeResolver([
+                 // Convert libphonenumber object to a string
+                 // PhpBaseType is used to support both Dart/TypeScript
+                 'PhoneNumber' => PhpBaseType::string(), 
+                 // Convert PHP Money object to a custom TypeScript type
+                 // It's TS-only syntax, to support Dart and the rest of the languages you'd have to create a separate PHP class like MoneyOutput
+                 'Money' => '{ amount: number; currency: string }',
+                 // Convert Doctrine Embeddable to an existing Dto marked as #[Dto]
+                 'SomeDoctrineEmbeddable' => 'SomeDoctrineEmbeddableDto',
+               ])
            ],
        ),
        new Filesystem(),
        new OutputDiffCalculator(),
        new FileSystemCodeProvider('/\.php$/'),
    )
);
```

### How to customize generated output file?

You may want to apply some transformations on the resulted file with types. For example, you may want to format it with tool of your choice or prepend code with a warning like "// The file was autogenerated, don't edit it manually". To add such a warning you can already use the built-in extension:

```diff
$application->add(
    new ConvertCommand(
        new Converter([
            new DtoVisitor(new PhpAttributeFilter('Dto')),
        ]),
        new TypeScriptGenerator(
            new SingleFileOutputWriter('generated.ts'),
            [
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
            ],
+           new OutputFilesProcessor([
+               new PrependAutogeneratedNoticeFileProcessor(),
+           ]),
        ),
        new Filesystem(),
        new OutputDiffCalculator(),
        new FileSystemCodeProvider('/\.php$/'),
    )
);
```

Feel free to create your own processor based on [PrependAutogeneratedNoticeFileProcessor](https://github.com/riverwaysoft/php-converter/blob/26ee25f07ac97a942e1327165424fc65777b80b0/src/OutputWriter/OutputProcessor/PrependAutogeneratedNoticeFileProcessor.php) source.

Here is an example how [Prettier](https://prettier.io/) formatter could look like:

```php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PrettierFormatProcessor implements SingleOutputFileProcessorInterface
{
    public function process(OutputFile $outputFile): OutputFile
    {
        $fs = new Filesystem();
        $temporaryGeneratedFile = $fs->tempnam(sys_get_temp_dir(), "dto", '.ts');
        $fs->appendToFile($temporaryGeneratedFile, $outputFile->getContent());

        $process = new Process(["./node_modules/.bin/prettier", $temporaryGeneratedFile, '--write', '--config', '.prettierrc.js']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return new OutputFile(
            relativeName: $outputFile->getRelativeName(),
            content: file_get_contents($temporaryGeneratedFile)
        );
    }
}
```

Then add it to the list:

```diff
$application->add(
    new ConvertCommand(
        new Converter([
            new DtoVisitor(new PhpAttributeFilter('Dto')),
        ]),
        new TypeScriptGenerator(
            new SingleFileOutputWriter('generated.ts'),
            [
                new DateTimeTypeResolver(),
                new ClassNameTypeResolver(),
            ],
+           new OutputFilesProcessor([
+               new PrependAutogeneratedNoticeFileProcessor(),
+               new PrettierFormatProcessor(),
+           ]),
        ),
        new Filesystem(),
        new OutputDiffCalculator(),
        new FileSystemCodeProvider('/\.php$/'),
    )
);
```

### How to add support for other languages?
To write a custom converter you can implement [LanguageGeneratorInterface](./src/Language/LanguageGeneratorInterface.php). Here is an example how to do it for Go language: [GoGeneratorSimple](./tests/GoGeneratorSimple.php). Check how to use it [here](./tests/GoGeneratorSimpleTest.php). It covers only basic scenarios to get you an idea, so feel free to modify it to your needs.

## Error list
Here is a list of errors `php-converter` can throw and description what to do if you encounter these errors:

### 1. Property z of class X has no type. Please add PHP type
It means that you've forgotten to add type for property `a` of class Y. Example:

```php
#[Dto]
class X {
  public $z;
} 
```

At the moment there is no strict / loose mode in `php-converter`. It is always strict. If you don't know the PHP type just use [mixed](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.mixed) type to explicitly convert it to `any`/`Object`. It could silently convert such types to TypeScript `any` or Dart `Object` if we needed it. But we prefer an explicit approach. Feel free to raise an issue if having loose mode makes sense for you.


### 2. PHP Type X is not supported
It means `php-converter` doesn't know how to convert the type X into TypeScript or Dart. If you are using `#[Dto]` attribute you probably forgot to add it to class `X`. Example:

```php
#[Dto]
class A {
  public X $x;
}

class X {
  public int $foo;
}
```

## Testing

``` bash
composer test
```

## How it is different from alternatives?
- Unlike [spatie/typescript-transformer](https://github.com/spatie/typescript-transformer) `php-converter` supports not only TypeScript but also Dart. Support for other languages can be easily added by implementing LanguageInterface. `php-converter` can also output generated types / classes into different files.
- Unlike [grpc](https://github.com/grpc/grpc/tree/v1.40.0/examples/php) `php-converter` doesn't require to modify your app or install some extensions.

## Contributing

Please see [CONTRIBUTING](./CONTRIBUTING.md) for details.

## Development

### Profile

Show memory & time usage:

`bin/dto-converter generate --from=./ --to=./assets/ -v`

Generate Xdebug profiler output:

`php -d xdebug.mode=profile -d xdebug.output_dir=. bin/dto-converter generate --from=./ --to=./assets/ -v -xdebug`

Then open the result .cachegrind file in PHPStorm -> Tools -> Analyze XDebug Profiler Snapshot
