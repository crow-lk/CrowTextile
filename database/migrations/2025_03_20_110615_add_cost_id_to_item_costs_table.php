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
        Schema::table('item_costs', function (Blueprint $table) {
            $table->foreignId('cost_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_costs', function (Blueprint $table) {
            $table->dropForeign(['cost_id']); // Specify the column name in an array
            $table->dropColumn('cost_id'); // Optionally drop the column if you want to remove it
        });
    }
};
