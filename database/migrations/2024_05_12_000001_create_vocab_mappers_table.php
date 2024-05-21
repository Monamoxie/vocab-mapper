<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVocabMappersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('vocab_mappers', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('vocab_id')->unsigned();
            $table->foreign('vocab_id')->references('id')->on('vocabs')->onDelete('cascade');

            if (config('vocab.entity_has_uuid')) {
                $table->uuidMorphs('entity');
            } else {
                $table->morphs('entity');
            }

            $table->string('custom_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('vocab_mappers');
    }
}
