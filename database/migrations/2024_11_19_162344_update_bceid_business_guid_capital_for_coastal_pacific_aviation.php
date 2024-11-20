<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('institutions')
            ->where('bceid_business_guid', '474142177638458993db885d57333662')
            ->update(['bceid_business_guid' => '474142177638458993DB885D57333662']);

        DB::table('users')
            ->where('bceid_business_guid', '474142177638458993DB885D57333662')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
