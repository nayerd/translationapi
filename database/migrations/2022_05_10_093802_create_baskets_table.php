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
        Schema::create('baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable(true)->default(null)->constrained('projects')->onUpdate('cascade');
            $table->foreignId('customer_id')->nullable(true)->default(null)->constrained('customers')->onUpdate('cascade');
            $table->dateTime('due_date')->nullable(true)->default(null);
            $table->decimal('basket_price', 15, 4)->nullable(true)->default(null);
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
        Schema::dropIfExists('baskets');
    }
};
