<?php

declare(strict_types=1);

namespace Riverwaysoft\PhpConverter\OutputGenerator\Go;

use Riverwaysoft\PhpConverter\OutputWriter\OutputProcessor\OutputFilesProcessor;
use Riverwaysoft\PhpConverter\OutputWriter\OutputProcessor\PrependAutogeneratedNoticeFileProcessor;

class OutputFilesProcessorProvider
{
    public static function provide(?OutputFilesProcessor $outputFilesProcessor = null): OutputFilesProcessor
    {
        if (null !== $outputFilesProcessor) {
            return $outputFilesProcessor;
        }

        return new OutputFilesProcessor(
            [
                new PrependAutogeneratedNoticeFileProcessor(
                    text: <<<TEXT
// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!
// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!
// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!

package gen


TEXT
                ),
            ]
        );
    }
}