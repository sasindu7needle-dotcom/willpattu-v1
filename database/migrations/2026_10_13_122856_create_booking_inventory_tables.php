<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->text('description');
            $table->unsignedSmallInteger('inventory');
            $table->unsignedSmallInteger('capacity');
            $table->decimal('local_rate', 12, 2);
            $table->decimal('international_rate', 12, 2);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->uuid('booking_token')->unique();
            $table->string('property')->default('makara');
            $table->date('arrival');
            $table->date('departure');
            $table->string('nationality');
            $table->unsignedSmallInteger('adults');
            $table->unsignedSmallInteger('children')->default(0);
            $table->string('name', 120);
            $table->string('email', 254);
            $table->string('phone', 30);
            $table->text('requests')->nullable();
            $table->string('status')->default('confirmed');
            $table->string('currency', 3)->default('LKR');
            $table->decimal('total', 14, 2);
            $table->timestamp('consented_at');
            $table->timestamps();
            $table->index(['status', 'arrival', 'departure']);
        });
        Schema::create('booking_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->string('room_name', 120);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('nightly_rate', 12, 2);
            $table->decimal('subtotal', 14, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('room_types');
    }
};
