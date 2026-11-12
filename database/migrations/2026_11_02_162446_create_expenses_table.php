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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column:'user_id')->constrained()->cascadeOnDelete();
            $table->foreignId(column:'category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal(column:'amount', total:10, places:2);
            $table->string(column:'title');
            $table->text(column:'description')->nullable();
            $table->date(column:'date');
            $table->enum(column:'type', allowed:['one-time', 'recurring'])->default(value:'one-time');

            //recuring expenses
            $table->enum(column:'recurring_frequency', allowed:['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->date(column:'recurring_start_date')->nullable();
            $table->date(column:'recurring_end_date')->nullable();
            $table->foreignId(column:'parent_expense_id')->nullable()->constrained(table:'expenses')->nullOnDelete();
            $table->boolean(column:'is_auto_generated')->default(value:false);


            $table->timestamps();
            $table->softDeletes();
            $table->index(columns:['user_id','date']);
            $table->index(columns:['user_id','type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
