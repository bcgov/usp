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
        if (! Schema::hasColumn('institution_staff', 'comment')) {
            Schema::table('institution_staff', function (Blueprint $table) {
                $table->text('comment')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('institution_staff', 'comment')) {
            Schema::table('institution_staff', function (Blueprint $table) {
                $table->dropColumn('comment');
            });
        }
    }
};
