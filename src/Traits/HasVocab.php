<?php

namespace Monamoxie\VocabMapper\Traits;

use Monamoxie\VocabMapper\Contracts\VocabInterface;
use Monamoxie\VocabMapper\Vocab;
use Monamoxie\VocabMapper\VocabMapper;

trait HasVocab
{

    protected static VocabInterface $vocabService;

    protected static Vocab $vocab;

    protected static VocabMapper $vocabMapper;

    /**
     * Map custom vocabs for display
     *
     */
    public static function bootHasVocab()
    {
        static::$vocabService = app(VocabInterface::class);
        static::$vocab = app(Vocab::class);
        static::$vocabMapper = app(VocabMapper::class);
    }


    public function createVocab(?string $name = null): Vocab
    {
        $defaulName = !(empty($name)) ? $name : $this->getTable();
        static::$vocab = static::$vocabService->createVocab(handler: static::class, defaultName: $defaulName);

        return static::$vocab;
    }

    public function mapVocabTo(object $entity, string $as)
    {
        return static::$vocabService->mapVocabTo(
            entity: $entity,
            as: $as,
            handler: static::class,
            defaultName: $this->getTable()
        );
    }


    public function getVocab(): ?Vocab
    {
        return static::$vocab = static::$vocabService->getVocabByHandler(handler: static::class);
    }

    public function getVocabFor(object $entity)
    {
        return static::$vocabService->getVocabFor(entity: $entity, handler: static::class);
    }
}
