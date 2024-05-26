<?php

namespace Monamoxie\VocabMapper\Traits;

trait VocabHelper
{
    public function getMigrationDirectory(): string
    {
        $main = database_path('/migrations');
        $subDir = config('vocab.migration_sub_dir', '');

        if (!empty($subDir)) {
            $subDir = ltrim(rtrim($subDir, "/"), "/");
        }

        return sprintf('%s/%s', $main, $subDir);
    }

    public function shouldLoadPackageMigration(): bool
    {
        $migrationDir = $this->getMigrationDirectory();

        return !is_dir($migrationDir) ||
            !count(glob($migrationDir . '/*_create_vocabs_table.php')) ||
            !count(glob($migrationDir . '/*_create_vocab_mappers_table.php'));
    }
}
