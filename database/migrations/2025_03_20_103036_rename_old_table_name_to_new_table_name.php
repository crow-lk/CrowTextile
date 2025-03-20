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
        Schema::rename('parts', 'costs');
        Schema::rename('item_parts', 'item_costs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('costs', 'parts');
        Schema::rename('item_costs', 'item_parts');
    }
};
