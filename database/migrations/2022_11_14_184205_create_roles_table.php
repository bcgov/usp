<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Import the DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert default records
        DB::table('roles')->insert(['name' => 'Super Admin']);
        DB::table('roles')->insert(['name' => 'VSS Admin']);
        DB::table('roles')->insert(['name' => 'YEAF Admin']);
        DB::table('roles')->insert(['name' => 'TWP Admin']);
        DB::table('roles')->insert(['name' => 'NEB Admin']);

        DB::table('roles')->insert(['name' => 'VSS User']);
        DB::table('roles')->insert(['name' => 'YEAF User']);
        DB::table('roles')->insert(['name' => 'TWP User']);
        DB::table('roles')->insert(['name' => 'NEB User']);

        DB::table('roles')->insert(['name' => 'VSS Guest']);
        DB::table('roles')->insert(['name' => 'YEAF Guest']);
        DB::table('roles')->insert(['name' => 'TWP Guest']);
        DB::table('roles')->insert(['name' => 'NEB Guest']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
