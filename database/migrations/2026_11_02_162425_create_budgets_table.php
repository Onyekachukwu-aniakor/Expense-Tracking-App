<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column:'user_id')->constrained()->cascadeOnDelete();
            $table->foreignId(column:'category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal(column:'amount', total:10, places:2);
            $table->integer(column:'month');
            $table->integer(column:'year');
            $table->timestamps();

            $table->unique(columns:['user_id', 'category_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
