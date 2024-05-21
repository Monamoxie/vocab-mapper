<?php

namespace Monamoxie\VocabMapper;

use Illuminate\Database\Eloquent\Model;

class Vocab extends Model
{
    protected $fillable = [
        'default_name', 'handler'
    ];

    public function vocabMapper()
    {
        return $this->hasMany(VocabMapper::class);
    }
}
