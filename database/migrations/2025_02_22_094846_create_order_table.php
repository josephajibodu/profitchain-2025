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
            $table->id();

            $table->string('reference');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('sell_advert_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->integer('coin_amount');
            $table->unsignedBigInteger('total_amount');
            $table->integer('seller_unit_price');
            $table->integer('payment_time_limit')->comment('seller can only cancel when elapsed. should be in seconds');
            $table->string('status')->comment('Pending, Paid, Completed, Cancelled, Disputed');
            $table->string('payment_proof')->nullable();

            $table->timestamps();
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
