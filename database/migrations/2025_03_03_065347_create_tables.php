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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('credit_balance', 10, 2)->default(0);
            $table->string('payment_status')->default('Unpaid');
            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('qty')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('item_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            //forgien key with invoices table
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            //payment method
            $table->string('payment_method')->nullable();
            //reference number
            $table->string('reference_number')->nullable();
            //payment date
            $table->date('payment_date')->useCurrent();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_parts');
        Schema::dropIfExists('payments');
    }
};
