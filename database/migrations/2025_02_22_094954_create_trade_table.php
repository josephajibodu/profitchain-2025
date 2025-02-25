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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();

            $table->string('reference');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('currency_pair_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('currency_pair_name');
            $table->unsignedBigInteger('capital');
            $table->unsignedBigInteger('total_roi');
            $table->integer('margin_applied');
            $table->integer('payment_time_limit')->comment('seller can only cancel when elapsed. should be in seconds');
            $table->string('status')->comment('Trading, Completed, Paid Out, Cancelled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
