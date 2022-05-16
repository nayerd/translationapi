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
        Schema::create('basket_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basket_id')->nullable(true)->default(null)->constrained('baskets')->onUpdate('cascade');
            $table->foreignId('document_id')->nullable(true)->default(null)->constrained('documents')->onUpdate('cascade');
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
        Schema::dropIfExists('basket_documents');
    }
};
