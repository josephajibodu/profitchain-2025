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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('reference');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_sent')->comment('amount after fee deduction');
            $table->unsignedInteger('rate')->default(0)->after('fee');
            $table->unsignedInteger('amount_payable')->default(0)->after('rate');
            $table->float('fee')->nullable()->default(0)->comment('Fee used for the withdrawal');
            $table->string('status');
            $table->string('bank_name');
            $table->string('bank_account_name');
            $table->string('bank_account_number');
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
        Schema::dropIfExists('withdrawals');
    }
};
