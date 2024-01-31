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

            $table->string('guid', 32)->index()->unique();

            $table->string('fed_cap_guid', 32);
            $table->foreign('fed_cap_guid')->references('guid')->on('fed_caps')
                ->onDelete('cascade');

            $table->string('institution_guid', 32);
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');

            $table->integer('total_attestations');

            $table->string('program_guid', 32)->nullable();
            $table->string('campus_guid', 32)->nullable();

            //updated is for when the staff trigger relocation of attestations from one institution to another
            //both the to/from institutions old cap tables will be switched to status=updated
            //both the to/from institutions new cap tables will have the same guid as the old records
            $table->string('status')->default('active')->comment('active|completed|updated');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('caps');
    }
};
