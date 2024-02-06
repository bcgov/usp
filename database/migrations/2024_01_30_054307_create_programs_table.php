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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('guid', 32)->index()->unique();

            $table->string('institution_guid', 32);
            $table->foreign('institution_guid')->references('guid')->on('institutions')
                ->onDelete('cascade');

            $table->string('program_name');
            $table->string('program_type');
            $table->string('credential')->nullable();
            $table->float('total_duration_hrs')->default(0)->nullable();
            $table->float('total_duration_weeks')->default(0)->nullable();
            $table->float('tuition_domestic')->default(0)->nullable();
            $table->float('tuition_international')->default(0)->nullable();
            $table->boolean('work_experience_required')->default(false)->nullable();
            $table->boolean('delivery_in_class')->default(false)->nullable();
            $table->boolean('delivery_distance')->default(false)->nullable();
            $table->boolean('delivery_combined')->default(false)->nullable();

            $table->string('noc_code')->nullable()->comment('national occupation code. NOC codes are used by training institutes');
            $table->string('cip_code')->nullable()->comment('granting institutions use CIP codes to categorize programs');
            $table->string('status')->comment('institution status set by inst.: active, inactive');
            $table->string('restrictions')->nullable()->comment('status set by the ministry');
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
        Schema::dropIfExists('programs');
    }
};
