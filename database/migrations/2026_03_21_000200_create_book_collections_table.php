<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_collections', function (Blueprint $table) {
            $table->id();
            $table->string('book_name', 255);
            $table->string('author', 255);
            $table->string('book_type', 100);
            $table->string('cover_image_path', 500);
            $table->string('notes', 1000)->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();

            $table->index('book_name');
            $table->index('author');
            $table->index('book_type');
            $table->index('created_at');
            $table->index('added_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_collections');
    }
};
