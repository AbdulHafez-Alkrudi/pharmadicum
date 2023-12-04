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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('company_id')->constrained();

            $table->string('scientific_name_EN');
            $table->string('economic_name_EN');
            $table->string('scientific_name_AR');
            $table->string('economic_name_AR');
            $table->string('image')->nullable();
            $table->integer('unit_price');
            $table->timestamps();

           /* $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('company_id')->references('id')->on('companies');*/

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
