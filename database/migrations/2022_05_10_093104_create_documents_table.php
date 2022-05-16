<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This migration creates the documents table
     *
     * A document is an object that contains words, grouped in sentences that belongs to a language
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_id', 255)->nullable(true)->default(null);
            $table->string('file_name', 255)->nullable(true)->default(null);
            $table->string('file_type', 255)->nullable(true)->default(null);
            $table->longText('file_content')->nullable(true)->default(null);
            $table->longText('file_comments')->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table){
            $table->dropForeign(['language_id']);
            $table->dropIndex('documents_language_id_languages_foreign');
        });

        Schema::dropIfExists('documents');
    }
};
