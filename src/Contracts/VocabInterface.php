<?php

namespace Monamoxie\VocabMapper\Contracts;

use Monamoxie\VocabMapper\Vocab;
use Monamoxie\VocabMapper\VocabMapper;

interface VocabInterface
{
    public function createVocab(string $handler, string $defaultName): Vocab;

    public function getOrCreateVocabByHandler(string $handler, string $defaulName): Vocab;

    public function getVocabByHandler($handler): ?Vocab;

    public function mapVocabTo(object $entity, string $as, string $handler, string $defaultName): ?VocabMapper;

    public function getVocabFor(object $entity, string $handler): ?VocabMapper;
}
