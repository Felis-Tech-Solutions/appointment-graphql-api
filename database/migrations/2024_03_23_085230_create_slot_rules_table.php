<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slot_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name')->nullable();
            $table->string('day_of_the_week')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('slot_duration')->nullable()->comment('In Milliseconds');
            $table->integer('time_after_appointment')->nullable()->comment('In Milliseconds');
            $table->foreignId('appointment_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_rules');
    }
};
