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

            $table->uuid('guid')->index();

            $table->uuid('user_guid');
            $table->foreign('user_guid')->references('guid')->on('users')
                ->onDelete('cascade');

            $table->uuid('institution_guid');
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');

            $table->string('status')->default('pending')->nullable();

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
