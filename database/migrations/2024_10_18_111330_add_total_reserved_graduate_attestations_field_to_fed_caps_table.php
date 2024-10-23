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
        Schema::table('fed_caps', function (Blueprint $table) {
            $table->integer('total_reserved_graduate_attestations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fed_caps', function (Blueprint $table) {
            $table->dropColumn('total_reserved_graduate_attestations');
        });
    }
};
