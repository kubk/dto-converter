<?php

declare(strict_types=1);

namespace Riverwaysoft\PhpConverter\Config;

use Exception;
use Riverwaysoft\PhpConverter\Ast\ConverterVisitor;
use Riverwaysoft\PhpConverter\CodeProvider\CodeProviderInterface;
use Riverwaysoft\PhpConverter\CodeProvider\FileSystemCodeProvider;
use Riverwaysoft\PhpConverter\CodeProvider\RemoteRepositoryCodeProvider;
use Riverwaysoft\PhpConverter\OutputGenerator\OutputGeneratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class PhpConverterConfig
{
    /** @var ConverterVisitor[] */
    private array $visitors = [];

    private OutputGeneratorInterface|null $outputGenerator = null;

    private CodeProviderInterface|null $codeProvider = null;

    private string|null $toDirectory = null;

    public function __construct(
        private InputInterface $input,
    ) {
    }

    public function addVisitor(ConverterVisitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    /** @return ConverterVisitor[] */
    public function getVisitors(): array
    {
        return $this->visitors;
    }

    public function setOutputGenerator(OutputGeneratorInterface $generator): void
    {
        $this->outputGenerator = $generator;
    }

    public function getOutputGenerator(): OutputGeneratorInterface
    {
        return $this->outputGenerator;
    }

    public function setCodeProvider(CodeProviderInterface $codeProvider): void
    {
        $this->codeProvider = $codeProvider;
    }

    public function getCodeProvider(): CodeProviderInterface
    {
        if (!$this->codeProvider) {
            $this->codeProvider = $this->guessCodeProvider();
        }

        return $this->codeProvider;
    }

    private function guessCodeProvider(): CodeProviderInterface
    {
        $from = $this->input->getOption('from');

        if (is_dir($from)) {
            return FileSystemCodeProvider::phpFiles($from);
        }

        if ((str_starts_with(haystack: $from, needle: 'https://') || str_starts_with(haystack: $from, needle: 'git@'))
            && str_ends_with(haystack: $from, needle: '.git')) {
            $branch = $this->input->getOption('branch');
            if (!$branch) {
                throw new InvalidArgumentException('Option --branch is required when using URL as repository source');
            }

            return new RemoteRepositoryCodeProvider(repositoryUrl: $from, branch: $branch);
        }

        throw new Exception(sprintf("Either pass --from as CLI argument or set the code provider via %s::setCodeProvider()", self::class));
    }

    public function getToDirectory(): string
    {
        $to = $this->toDirectory ?: $this->input->getOption('to');

        Assert::directory($to, sprintf("Either pass --to as CLI argument or set the directory via %s::setToDirectory()", self::class));

        return $to;
    }

    public function setToDirectory(string $toDirectory): void
    {
        $this->toDirectory = $toDirectory;
    }
}
