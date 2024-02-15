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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('guid', 32)->index()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('disabled')->default(false);

            $table->string('email');
            $table->string('password');
            $table->string('idir_username', 60)->nullable();
            $table->string('bceid_username', 60)->nullable();
            $table->string('idir_user_guid')->nullable();
            $table->string('bceid_user_guid')->nullable();
            $table->string('bceid_business_guid')->nullable();
            $table->string('last_touch_by_user_guid', 32)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
