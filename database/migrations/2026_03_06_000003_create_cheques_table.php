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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('direction');
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('party_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('cheque_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('remind_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
