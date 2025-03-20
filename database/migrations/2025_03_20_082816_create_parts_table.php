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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('item_parts', function (Blueprint $table) {
            $table->foreignId('part_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_parts', function (Blueprint $table) {
            $table->dropForeign(['part_id']); // Specify the column name in an array
            $table->dropColumn('part_id'); // Optionally drop the column if you want to remove it
        });
        Schema::dropIfExists('parts');
    }
};
