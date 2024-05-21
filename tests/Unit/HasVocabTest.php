<?php

namespace Monamoxie\VocabMapper\Tests\Unit;

use Monamoxie\VocabMapper\Vocab;
use Monamoxie\VocabMapper\VocabMapper;
use Monamoxie\VocabMapper\Tests\TestCase;

final class HasVocabTest extends TestCase
{

    public function testCreateVocab()
    {
        $providedName = fake()->lastName;

        $vocab = $this->model->createVocab($providedName);

        $this->assertInstanceOf(Vocab::class, $vocab);
        $this->assertEquals($providedName, $vocab->default_name);
    }

    public function testCreateVocabWithNoName()
    {
        $vocab = $this->model->createVocab();

        $this->assertInstanceOf(Vocab::class, $vocab);
        $this->assertEquals($this->model->getTable(), $vocab->default_name);
    }

    public function testMapVocabTo()
    {
        $user = $this->makeUser();

        $this->assertInstanceOf(config('vocab.entity_model'), $user);
        $customName = fake()->domainWord;

        $vocabMapper = $this->model->mapVocabTo($user, $customName);

        $this->assertInstanceOf(VocabMapper::class, $vocabMapper);
        $this->assertEquals($customName, $vocabMapper->custom_name);
    }


    public function testGetVocab()
    {
        $user = $this->makeUser();
        $this->model->mapVocabTo($user, fake()->domainWord);

        $modelVocab = $this->model->getVocab();
        $this->assertInstanceOf(Vocab::class, $modelVocab);
    }

    public function testGetVocabFor()
    {
        $user = $this->makeUser();
        $this->model->mapVocabTo($user, fake()->domainWord);

        $userVocab = $this->model->getVocabFor($user);
        $this->assertInstanceOf(VocabMapper::class, $userVocab);
    }
}
