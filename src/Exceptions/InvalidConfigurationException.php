<?php

namespace Monamoxie\VocabMapper\Exceptions;

use Exception;

class InvalidConfigurationException extends Exception
{
    public function __construct(string $key, string $typeProvided, array $typeExpected)
    {
        $typeExpected = collect($typeExpected)->implode(',');

        return parent::__construct("Invalid configuration provided for {$key}. `{$typeProvided}` provided, but `{$typeExpected}` expected");
    }
}
