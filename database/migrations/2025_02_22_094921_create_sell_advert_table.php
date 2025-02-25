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
        Schema::create('sell_adverts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('available_balance');
            $table->integer('remaining_balance');
            $table->integer('unit_price');
            $table->unsignedBigInteger('minimum_sell');
            $table->unsignedBigInteger('max_sell');
            $table->string('status')->comment('Available, Sold Out');
            $table->boolean('is_published')->default(true);
            $table->string('bank_name');
            $table->string('bank_account_name');
            $table->string('bank_account_number');
            $table->text('terms');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_adverts');
    }
};
