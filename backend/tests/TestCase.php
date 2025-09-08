<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        if (!app()->environment('testing')) {
            throw new \RuntimeException('Tests must run in testing environment.');
        }

        $db = DB::select('select current_database() as db')[0]->db ?? '';
        if (strpos($db, 'test') === false) {
            throw new \RuntimeException("Unsafe test database name: {$db}");
        }
    }
}
