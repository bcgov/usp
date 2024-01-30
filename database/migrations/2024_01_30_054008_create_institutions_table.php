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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->index()->unique();
            $table->string('dli')->nullable();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('primary_email');
            $table->string('secondary_email')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('public')->default(false);
            $table->boolean('active_status')->default(false);
            $table->string('standing_status')->nullable();

            $table->string('api_id')->nullable();
            $table->string('api_key')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
