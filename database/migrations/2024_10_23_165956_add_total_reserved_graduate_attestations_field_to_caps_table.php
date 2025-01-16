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
        Schema::table('caps', function (Blueprint $table) {
            $table->integer('total_reserved_graduate_attestations')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caps', function (Blueprint $table) {
            $table->dropColumn('total_reserved_graduate_attestations');
        });
    }
};
