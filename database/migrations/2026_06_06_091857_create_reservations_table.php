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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('guest_name');

            $table->enum('channel', [
                'Airbnb',
                'Booking.com',
                'Agoda',
                'Direct'
            ]);

            $table->date('check_in');
            $table->date('check_out');

            $table->decimal('total_price', 10, 2)
                ->default(0);

            $table->enum('status', [
                'Confirmed',
                'Checked In',
                'Checked Out',
                'Cancelled'
            ])->default('Confirmed');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};