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
        Schema::create('currency_pair', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('margin');
            $table->unsignedInteger('trade_duration');
            $table->unsignedBigInteger('daily_capacity');
            $table->unsignedBigInteger('current_capacity');
            $table->string('status')->comment('open/close');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_pair');
    }
};
