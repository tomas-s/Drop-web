<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSensorHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sensor_id');
            $table->foreign('sensor_id')->references('sensor_id')->on('sensors');
            $table->string('state');
            $table->integer('battery');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sensor_history');
    }
}
