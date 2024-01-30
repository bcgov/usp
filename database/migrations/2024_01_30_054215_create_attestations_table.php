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

            $table->uuid('guid')->index();

            $table->uuid('institution_guid');
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');
            $table->string('program_year', 10);
            $table->string('student_name');
            $table->string('student_id_number');
            $table->string('student_dob');
            $table->string('status')->default('new');
            $table->date('expiry_date')->default(now()->addDays(30));

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
