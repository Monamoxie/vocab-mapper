<?php

namespace Monamoxie\VocabMapper\Facades;

use Illuminate\Support\Facades\Facade;

class VocabCollector extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'VocabCollector';
    }
}
