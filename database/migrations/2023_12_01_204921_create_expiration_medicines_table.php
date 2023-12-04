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
        Schema::create('expiration_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId("medicine_id")->constrained()->cascadeOnDelete();
            $table->integer("quantity");
            $table->date("expiration_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_expirations');
    }
};
