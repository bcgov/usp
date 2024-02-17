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
        Schema::create('institution_staff', function (Blueprint $table) {
            $table->id();

            $table->string('guid', 32)->index()->unique();
            $table->string('user_guid', 32);
            $table->foreign('user_guid')->references('guid')->on('users')
                ->onDelete('cascade');

            $table->string('institution_guid', 32);
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');

            $table->string('bceid_business_guid');
            $table->string('bceid_user_guid');
            $table->string('bceid_user_id');
            $table->string('bceid_user_name')->nullable();
            $table->string('bceid_user_email')->nullable();

            $table->string('status')->default('pending')->nullable();
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
        Schema::dropIfExists('institution_staff');
    }
};
