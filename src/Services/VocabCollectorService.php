<?php

namespace Monamoxie\VocabMapper\Services;

use Monamoxie\VocabMapper\Contracts\VocabInterface;
use Monamoxie\VocabMapper\Data\VocabResponse;
use Monamoxie\VocabMapper\Vocab;

class VocabCollectorService
{
    protected VocabInterface $vocabService;

    protected VocabResponse $response;

    public function __construct(VocabInterface $vocabService)
    {
        $this->vocabService = $vocabService;
    }

    /**
     * Get the vocab assigned for a particular user entitiy
     *
     * @param object $user The user entity
     * @param Vocab $vocab The vocab instance
     *
     * @return VocabResponse
     */
    public function getFor(object $user, string $handler): VocabResponse
    {
        $this->response = new VocabResponse($this->vocabService->getVocabFor($user, $handler));

        return $this->response;
    }
}
