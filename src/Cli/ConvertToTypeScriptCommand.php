<?php

declare(strict_types=1);

namespace Riverwaysoft\DtoConverter\Cli;

use Riverwaysoft\DtoConverter\CodeProvider\FileSystemCodeProvider;
use Riverwaysoft\DtoConverter\Converter;
use Riverwaysoft\DtoConverter\OutputDiffCalculator\OutputDiffCalculator;
use Riverwaysoft\DtoConverter\Language\TypeScript\TypeScriptGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

class ConvertToTypeScriptCommand extends Command
{
    protected static $defaultName = 'dto-generator:generate';

    public function __construct(
        private Converter $converter,
        private TypeScriptGenerator $typeScriptGenerator,
        private Filesystem $fileSystem,
        private FileSystemCodeProvider $codeProvider,
        private OutputDiffCalculator $diffWriter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate TypeScript DTO from PHP sources')
            ->addOption('from', 'f', InputOption::VALUE_REQUIRED)
            ->addOption('to', 't', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        Assert::directory($from);
        Assert::directory($to);

        $files = $this->codeProvider->getListings($from);
        $normalized = $this->converter->convert($files);
        $outputFiles = $this->typeScriptGenerator->generate($normalized);

        foreach ($outputFiles as $outputFile) {
            $outputAbsolutePath = rtrim($to, '/') . '/' . $outputFile->getRelativeName();
            if ($this->fileSystem->exists($outputAbsolutePath)) {
                $diff = $this->diffWriter->calculate(file_get_contents($outputAbsolutePath), $outputFile->getContent());
                if (empty($diff)) {
                    $output->writeln(sprintf("\nNo difference between the old generated file and the new one: %s", $outputFile->getRelativeName()));
                } else {
                    $output->writeln(sprintf("\nSuccessfully written file: %s", $outputFile->getRelativeName()));
                    $output->write($diff);
                }

                $this->fileSystem->remove($outputAbsolutePath);
            }
            $this->fileSystem->touch($outputAbsolutePath);
            $this->fileSystem->appendToFile($outputAbsolutePath, $outputFile->getContent());
        }

        return Command::SUCCESS;
    }
}