<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('thermostats', function (Blueprint $table) {
            $table->id();
            $table->boolean('online')->default(false);
            $table->string('mode');
            $table->unsignedInteger('current_temperature')->default(0);
            $table->unsignedInteger('expected_temperature')->default(15);
            $table->unsignedInteger('humidity')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('thermostats');
    }
};
