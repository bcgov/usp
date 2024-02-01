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
            $table->string('guid', 32)->index()->unique();
            $table->string('dli')->nullable();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('primary_contact');
            $table->string('primary_email');
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('province', 3)->nullable();
            $table->boolean('public')->default(false);
            $table->boolean('active_status')->default(false);
            $table->string('standing_status')->nullable();
            $table->string('api_id')->nullable();
            $table->string('api_key')->nullable();
            $table->string('last_touch_by_user_guid')->nullable();

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
