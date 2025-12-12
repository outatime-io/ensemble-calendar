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
        Schema::create('rehearsals', function (Blueprint $table) {
            $table->id();
            $table->uuid('ics_uid')->unique();
            $table->string('title');
            $table->string('location_name');
            $table->string('location_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('timezone')->default(config('app.timezone'));
            $table->date('start_date');
            $table->date('end_date');
            $table->string('plan_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rehearsals');
    }
};
