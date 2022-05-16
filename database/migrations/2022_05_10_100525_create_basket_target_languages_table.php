<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basket_target_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basket_id')->nullable(true)->default(null)->constrained('baskets')->onUpdate('cascade');
            $table->foreignId('language_id')->nullable(true)->default(null)->constrained('languages')->onUpdate('cascade');
            $table->decimal('translation_price', 15, 4)->nullable(true)->default(null);
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
        Schema::dropIfExists('basket_target_languages');
    }
};
