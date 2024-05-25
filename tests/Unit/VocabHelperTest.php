<?php

namespace Monamoxie\VocabMapper\Tests\Unit;

use Monamoxie\VocabMapper\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Monamoxie\VocabMapper\Traits\VocabHelper;

final class VocabHelperTest extends TestCase
{

    public function testMigrationSubPathUsingDefault()
    {
        $model = new class
        {
            use VocabHelper;
        };

        $this->assertEquals(database_path('/migrations/'), $model->getMigrationDirectory());
    }

    public function testMigrationSubPathUsingCustomPath()
    {
        $subDir = fake()->domainWord;

        Config::set('vocab.migration_sub_dir', '/' . $subDir . '/');

        $model = new class
        {
            use VocabHelper;
        };

        $this->assertEquals(database_path('/migrations/' . $subDir), $model->getMigrationDirectory());
    }
}
