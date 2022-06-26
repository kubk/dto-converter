<?php

declare(strict_types=1);

namespace App\Tests;

use Spatie\Snapshots\Drivers\TextDriver;

class DartSnapshotComparator extends TextDriver
{
    public function extension(): string
    {
        return 'dart';
    }
}
