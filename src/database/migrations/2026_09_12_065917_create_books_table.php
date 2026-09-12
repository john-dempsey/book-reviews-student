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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->year('year');
            $table->string('image')->nullable();

            // Part 1 keeps one implicit edition per book, rather than a
            // separate editions table - these four fields describe that
            // one edition directly.
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->string('edition_number')->nullable();
            $table->decimal('price', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
