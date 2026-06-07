<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housekeeping_tasks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('guest_name');

            $table->date('checkout_date');

            $table->enum('status', [
                'Pending',
                'In Progress',
                'Ready'
            ])->default('Pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housekeeping_tasks');
    }
};