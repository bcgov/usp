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
            $table->foreign('program_guid', 32)->references('guid')->on('programs')
                ->onDelete('cascade');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('id_number');
            $table->string('dob');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('province')->nullable();
            $table->string('country');


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
