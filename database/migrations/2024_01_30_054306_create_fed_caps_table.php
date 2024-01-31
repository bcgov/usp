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
        Schema::create('fed_caps', function (Blueprint $table) {
            $table->id();

            $table->string('guid', 32)->index()->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_attestations');

            $table->string('status')->default('active')->comment('active|completed|cancelled');
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
        Schema::dropIfExists('fed_caps');
    }
};
