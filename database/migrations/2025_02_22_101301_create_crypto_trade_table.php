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
        Schema::create('crypto_trades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('reference');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_sent')->comment('amount after fee deduction');
            $table->float('fee')->nullable()->default(0)->comment('Fee used for the withdrawal');
            $table->string('status');
            $table->string('network');
            $table->string('wallet_address');
            $table->string('comment')->nullable();
            $table->text('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_trades');
    }
};
