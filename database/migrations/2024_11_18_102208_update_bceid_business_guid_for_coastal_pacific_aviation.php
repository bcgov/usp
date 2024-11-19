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
            ->where('bceid_business_guid', '6FF00A0CAEE146C9A1F8A39A62A6BA40')
            ->update(['bceid_business_guid' => '474142177638458993db885d57333662']);

        DB::table('institutions')
            ->where('bceid_business_guid', '6ff00a0caee146c9a1f8a39a62a6ba40')
            ->update(['bceid_business_guid' => '474142177638458993db885d57333662']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('institutions')
            ->where('bceid_business_guid', '474142177638458993db885d57333662')
            ->update(['bceid_business_guid' => '6ff00a0caee146c9a1f8a39a62a6ba40']);
    }
};
