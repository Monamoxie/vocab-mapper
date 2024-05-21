<?php

namespace Monamoxie\VocabMapper;

use Illuminate\Database\Eloquent\Model;

class VocabMapper extends Model
{
    protected $fillable = [
        'vocab_id',
        'entity_type',
        'entity_id',
        'custom_name',
    ];

    public function vocab()
    {
        return $this->belongsTo(Vocab::class);
    }
}
