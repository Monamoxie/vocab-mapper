<?php

namespace Monamoxie\VocabMapper\Services;

use Monamoxie\VocabMapper\Contracts\VocabInterface;
use Monamoxie\VocabMapper\Exceptions\InvalidConfigurationException;
use Monamoxie\VocabMapper\Exceptions\InvalidEntityModelException;
use Monamoxie\VocabMapper\Vocab;
use Monamoxie\VocabMapper\VocabMapper;

class VocabService implements VocabInterface
{
    private array $config;

    private const EXPECTED_ENTITY_MODEL_ARGS = ['string', 'array'];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createVocab(string $handler, string $defaultName): Vocab
    {
        return Vocab::updateOrCreate([
            'default_name' => $defaultName,
            'handler' => $handler
        ], [
            'default_name' => $defaultName,
            'handler' => $handler
        ]);
    }

    public function getOrCreateVocabByHandler(string $handler, string $defaulName): Vocab
    {
        $vocab = $this->getVocabByHandler($handler);

        if (!$vocab) {
            $vocab = $this->createVocab(handler: $handler, defaultName: $defaulName);
        }

        return $vocab;
    }

    public function getVocabByHandler($handler): ?Vocab
    {
        return Vocab::where('handler', $handler)->first();
    }

    public function mapVocabTo(object $entity, string $as, string $handler, string $defaultName): VocabMapper
    {
        $vocab = $this->getOrCreateVocabByHandler($handler, $defaultName);

        if ($this->isValidEntityInstance($entity)) {
            $data = [
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
                'custom_name' => $as
            ];
        }

        return $vocab->vocabMapper()->updateOrCreate($data, $data);
    }

    public function getVocabFor(object $entity, string $handler): ?VocabMapper
    {
        if ($this->isValidEntityInstance($entity)) {
            if ($vocab = $this->getVocabByHandler($handler)) {
                return $vocab->vocabMapper()->where('entity_type', get_class($entity))
                    ->where('entity_id', $entity->id)->first();
            }
        }

        return null;
    }

    protected function isValidEntityInstance($entity): bool
    {
        $entityModelConfig = $this->config['entity_model'];
        $configType = gettype($entityModelConfig);

        if (!in_array($configType, self::EXPECTED_ENTITY_MODEL_ARGS)) {
            throw new InvalidConfigurationException('entity_model', $configType, self::EXPECTED_ENTITY_MODEL_ARGS);
        }

        $entityInstance = get_class($entity);
        $entityModelConfig = is_array($entityModelConfig) ? $entityModelConfig : [$entityModelConfig];

        collect($entityModelConfig)->each(function ($modelNamespace) use ($entityInstance, $entityModelConfig) {
            if ($entityInstance !== $modelNamespace) {
                throw new InvalidEntityModelException($entityInstance, $entityModelConfig);
            }
        });

        return true;
    }
}
