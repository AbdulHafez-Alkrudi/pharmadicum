<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('medicine_id')->constrained();
            $table->integer('amount');
            $table->integer('unit_price');

            $table->timestamps();

            /*$table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('medicine_id')->references('id')->on('medicines');*/

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
