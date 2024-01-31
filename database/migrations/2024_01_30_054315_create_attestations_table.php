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
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->string('guid', 32)->index()->unique();

            $table->string('institution_guid', 32);
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');
            $table->string('cap_guid', 32);
            $table->foreign('cap_guid')->references('guid')->on('caps')
                ->onDelete('cascade');

            $table->string('student_name');
            $table->string('student_id_number');
            $table->string('student_dob');
            $table->string('status')->default('new');
            $table->date('expiry_date')->default(now()->addDays(30));

            $table->string('created_by_user_guid')->nullable();
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
        Schema::dropIfExists('attestations');
    }
};
