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
        Schema::create('caps', function (Blueprint $table) {
            $table->id();

            $table->uuid('guid')->index();

            $table->uuid('institution_guid');
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');
            $table->string('program_year', 10);
            $table->integer('total_attestations');
            $table->integer('used_attestations');
            $table->integer('relocated_attestations');

            $table->uuid('relocated_from_institution_guid');
            $table->foreign('relocated_from_institution_guid')->references('guid')
                ->on('institutions')->onDelete('cascade');

            $table->uuid('relocated_to_institution_guid');
            $table->foreign('relocated_to_institution_guid')->references('guid')
                ->on('institutions')->onDelete('cascade');

            //updated is for when the staff trigger relocation of attestations from one institution to another
            //both the to/from institutions old cap tables will be switched to status=updated
            //both the to/from institutions new cap tables will have the same guid as the old records
            $table->string('status')->default('active')->comment('active|completed|updated');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caps');
    }
};
