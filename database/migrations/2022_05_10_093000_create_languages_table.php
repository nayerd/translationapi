<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the languages table
     *
     * A language is an object that represents an iso language and has a name (in english) and a native name (the name of the language in that language)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('iso')->nullable(true)->default(null);
            $table->string('name')->nullable(true)->default(null);
            $table->string('native_name')->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Drops the table
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language');
    }
};
