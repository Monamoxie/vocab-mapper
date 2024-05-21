<?php

namespace Monamoxie\VocabMapper\Tests\Unit;

use Orchestra\Testbench\Factories\UserFactory;
use Monamoxie\VocabMapper\Traits\HasVocab;
use Monamoxie\VocabMapper\Vocab;
use Monamoxie\VocabMapper\VocabMapper;
use Monamoxie\VocabMapper\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Monamoxie\VocabMapper\Contracts\VocabInterface;
use Monamoxie\VocabMapper\Exceptions\InvalidEntityModelException;
use Illuminate\Support\Facades\Config;
use Monamoxie\VocabMapper\Exceptions\InvalidConfigurationException;

final class VocabServiceTest extends TestCase
{
    public $model;

    private VocabInterface $vocabService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class extends Model
        {
            use HasVocab;
        };

        $this->vocabService = app(VocabInterface::class);
    }


    public function testCreateVocab()
    {
        $vocab = $this->vocabService->createVocab($this->model::class, fake()->domainWord);

        $this->assertInstanceOf(Vocab::class, $vocab);
    }

    public function testGetOrCreateVocabByHandler()
    {
        $defaultName = fake()->domainWord;

        $vocab = $this->vocabService->getOrCreateVocabByHandler($this->model::class, $defaultName);

        $this->assertInstanceOf(Vocab::class, $vocab);
        $this->assertEquals($defaultName, $vocab->default_name);
    }

    public function getVocabByHandler()
    {
        $defaultName = fake()->domainWord;
        $vocab = $this->vocabService->createVocab($this->model::class, $defaultName);
        $vocab = $this->vocabService->getVocabByHandler($this->model::class);

        $this->assertInstanceOf(Vocab::class, $vocab);
    }

    public function testMapVocabToForValidEntity()
    {
        $entity = $this->makeUser();
        $customName = fake()->domainWord;
        $defaultName = fake()->domainWord;

        $vocabMapper = $this->vocabService->mapVocabTo(
            entity: $entity,
            as: $customName,
            handler: $this->model::class,
            defaultName: $defaultName
        );

        $this->assertInstanceOf(VocabMapper::class, $vocabMapper);
    }

    public function testMapVocabToForInvalidEntity()
    {
        $wrongEntityInstance = UserFactory::new();

        $customName = fake()->domainWord;
        $defaultName = fake()->domainWord;

        // Define the expected exception
        $this->expectException(InvalidEntityModelException::class);

        $this->vocabService->mapVocabTo(
            entity: $wrongEntityInstance,
            as: $customName,
            handler: $this->model::class,
            defaultName: $defaultName
        );
    }

    public function testMapVocabToWithInvalidEntityModelConfiguration()
    {
        $wrongEntityInstance = UserFactory::new();

        Config::set('vocab.entity_model', fake()->numberBetween(100, 200));

        // reload the vocab service, so we can get the most updated value of the new config
        $this->vocabService = app(VocabInterface::class);

        $customName = fake()->domainWord;
        $defaultName = fake()->domainWord;

        // Define the expected exception
        $this->expectException(InvalidConfigurationException::class);

        $this->vocabService->mapVocabTo(
            entity: $wrongEntityInstance,
            as: $customName,
            handler: $this->model::class,
            defaultName: $defaultName
        );
    }

    public function testGetVocabForValidEntityWithData()
    {
        $entity = $this->makeUser();
        $customName = fake()->domainWord;
        $defaultName = fake()->domainWord;
        $handler = $this->model::class;

        $this->vocabService->mapVocabTo(
            entity: $entity,
            as: $customName,
            handler: $handler,
            defaultName: $defaultName
        );

        $entityVocab = $this->vocabService->getVocabFor($entity, $handler);
        $this->assertInstanceOf(VocabMapper::class, $entityVocab);
    }

    public function testGetVocabForValidEntityWithNoData()
    {
        $entity = $this->makeUser();
        $handler = $this->model::class;

        $entityVocab = $this->vocabService->getVocabFor($entity, $handler);
        $this->assertNull($entityVocab);
    }

    public function testGetVocabForInValidEntity()
    {
        $wrongEntityInstance = UserFactory::new();
        $handler = $this->model::class;

        $this->expectException(InvalidEntityModelException::class);

        $this->vocabService->getVocabFor($wrongEntityInstance, $handler);
    }
}
