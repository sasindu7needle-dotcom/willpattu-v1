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
        Schema::create('page_content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_content_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('meta');
            $table->text('text');
            $table->unsignedTinyInteger('image');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['page_content_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_content_items');
    }
};
