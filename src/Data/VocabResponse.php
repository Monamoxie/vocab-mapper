<?php

namespace Monamoxie\VocabMapper\Data;

use Monamoxie\VocabMapper\VocabMapper;
use Illuminate\Support\Str;

class VocabResponse
{
    protected array $attributes = [];

    public function __construct(null|VocabMapper $data)
    {
        if (!empty($data)) {
            if ($data instanceof VocabMapper) {
                $this->fromVocabMapper($data);
            }
        }
    }

    private function fromVocabMapper(VocabMapper $vocabMapper)
    {
        $key = $vocabMapper->vocab->default_name;
        $customName = $vocabMapper->custom_name;

        $vocabItem = new \stdClass;
        $vocabItem->custom_name = ucwords($customName);
        $vocabItem->singular = ucwords(Str::singular($customName));
        $vocabItem->plural = ucwords(Str::plural($customName));

        $this->attributes[$key] = $vocabItem;
    }

    /**
     * @todo ::: in a future release
     */
    // private function fromCollection(Collection $collection)
    // {
    //     foreach ($collection as $item) {
    //         if ($item instanceof VocabMapper) {
    //             $this->fromVocabMapper($item);
    //         }
    //     }
    // }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? $this;
    }
}
