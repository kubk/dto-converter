<?php

use App\CodeProvider\FileSystemCodeProvider;
use App\Converter;
use App\Language\TypeScriptGenerator;
use App\Normalizer;
use App\Testing\TypeScriptSnapshotComparator;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ConverterTest extends TestCase
{
    use MatchesSnapshots;

    public function testNormalization(): void
    {
        $converter = new Converter(Normalizer::factory(), new FileSystemCodeProvider(__DIR__ . '/fixtures'));
        $result = $converter->convert();
        $this->assertMatchesJsonSnapshot($result->getList());
        $this->assertMatchesSnapshot((new TypeScriptGenerator())->generate($result), new TypeScriptSnapshotComparator());
    }
}
