<?php

namespace Monamoxie\VocabMapper\Exceptions;

use Exception;

class InvalidEntityModelException extends Exception
{
    public function __construct(string $typeProvided, array $typeExpected)
    {
        $typeExpected = collect($typeExpected)->implode(',');

        parent::__construct("Invalid entity model provided. `{$typeProvided}` provided, but any of `{$typeExpected}` expected");
    }
}
