<?php

namespace Monamoxie\VocabMapper\Tests\Unit;

use Monamoxie\VocabMapper\Tests\TestCase;
use Monamoxie\VocabMapper\Data\VocabResponse;
use Monamoxie\VocabMapper\Services\VocabCollectorService;
use Illuminate\Support\Str;
use Monamoxie\VocabMapper\Facades\VocabCollector;

final class VocabCollectorServiceTest extends TestCase
{

    public function testCollectorWithDataResponse()
    {
        $entity = $this->makeUser();

        $customVocab = fake()->domainWord;
        $this->model->mapVocabTo($entity, $customVocab);

        $handler = $this->model::class;
        $defaultVocab = $this->model->getTable();

        $response = VocabCollector::getFor($entity, $handler);

        $this->assertInstanceOf(VocabResponse::class, $response);

        $row = $response->{$defaultVocab};
        $this->assertObjectHasProperty('custom_name', $row);

        $responseArray = $response->toArray();

        $this->assertArrayHasKey($defaultVocab, $responseArray);

        $this->assertEquals(ucwords($customVocab), $responseArray[$defaultVocab]->custom_name);
        $this->assertEquals(ucwords(Str::singular($customVocab)), $responseArray[$defaultVocab]->singular);
        $this->assertEquals(ucwords(Str::plural($customVocab)), $responseArray[$defaultVocab]->plural);
    }

    public function testCollectorWithNullResponse()
    {
        $user = $this->makeUser();

        $handler = $this->model::class;

        $vocabCollector = app(VocabCollectorService::class);
        $response = $vocabCollector->getFor($user, $handler);

        $this->assertInstanceOf(VocabResponse::class, $response);

        $this->assertEmpty($response->toArray());
    }
}
