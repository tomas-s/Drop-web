<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;



//update DB
//potrebne pridat zavislost ->  php composer.phar require doctrine/dbal

class State extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sensors', function (Blueprint $table) {
                    $table->boolean('state')->default(0)->change();
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
