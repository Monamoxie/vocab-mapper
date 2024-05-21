<?php

namespace Monamoxie\VocabMapper\Traits;

trait VocabHelper
{
    public function getMigrationDirectory(): string
    {
        $main = database_path('/database/migrations/');
        $subDir = config('vocab.migration_sub_dir', '');

        if (!empty($subDir)) {
            $subDir = ltrim(rtrim($subDir, "/"), "/");
        }

        return sprintf('%s%s', $main, $subDir);
    }
}
