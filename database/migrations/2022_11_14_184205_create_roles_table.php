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
        DB::table('roles')->insert(['name' => 'Ministry Admin']);
        DB::table('roles')->insert(['name' => 'Institution Admin']);

        DB::table('roles')->insert(['name' => 'Ministry User']);
        DB::table('roles')->insert(['name' => 'Institution User']);

        DB::table('roles')->insert(['name' => 'Ministry Guest']);
        DB::table('roles')->insert(['name' => 'Institution Guest']);

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
