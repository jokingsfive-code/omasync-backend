<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('priority', [
                'Low',
                'Medium',
                'High',
                'Critical'
            ])->default('Medium');

            $table->enum('status', [
                'Open',
                'In Progress',
                'Completed',
                'Cancelled'
            ])->default('Open');

            $table->string('reported_by')->nullable();
            $table->string('assigned_to')->nullable();

            $table->date('reported_date')->nullable();
            $table->date('completed_date')->nullable();

            $table->decimal('cost', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_tickets');
    }
};