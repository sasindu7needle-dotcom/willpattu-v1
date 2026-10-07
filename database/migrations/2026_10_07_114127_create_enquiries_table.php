<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->string('name', 120);
            $table->string('email', 254);
            $table->string('phone', 30)->nullable();
            $table->string('category');
            $table->date('arrival')->nullable();
            $table->date('departure')->nullable();
            $table->unsignedTinyInteger('travellers');
            $table->string('interest', 150)->nullable();
            $table->text('message');
            $table->string('status')->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
