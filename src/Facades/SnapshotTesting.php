<?php

namespace Sourcefli\SnapshotTesting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sourcefli\SnapshotTesting\SnapshotTesting
 */
class SnapshotTesting extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-snapshot-testing';
    }
}
