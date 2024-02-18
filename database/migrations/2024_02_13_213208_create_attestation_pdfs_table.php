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
        Schema::create('attestation_pdfs', function (Blueprint $table) {
            $table->id();
            $table->string('guid', 32)->index()->unique();
            $table->string('attestation_guid', 32);
            $table->foreign('attestation_guid')->references('guid')->on('attestations')
                ->onDelete('cascade');
            $table->text('content'); // Use longText to store large content
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attestation_pdfs');
    }
};
