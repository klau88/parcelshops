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
        Schema::create('parcelshops', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('name');
            $table->string('slug');
            $table->string('type');
            $table->string('street');
            $table->string('number');
            $table->string('postal_code');
            $table->string('city');
            $table->string('country');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelshops');
    }
};
