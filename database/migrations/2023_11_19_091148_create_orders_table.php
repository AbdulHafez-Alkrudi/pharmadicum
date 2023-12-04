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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('order_status_id')->default(1)->constrained();
            $table->foreignId('payment_status_id')->default(1)->constrained();
            $table->string('total_invoice');
            $table->timestamps();

            /*$table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('payment_status_id')->references('id')->on('payment_statuses');*/


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
