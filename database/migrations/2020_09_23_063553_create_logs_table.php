<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
          $table->increments('log_id');
          $table->integer('sender')->unsigned();
          $table->integer('receiver')->unsigned();
          $table->enum('logtype',['send','receive']);
          $table->dateTime('log_date');
          $table->string('remarks')->nullable();
          $table->timestamps();
        });

        Schema::table('logs', function (Blueprint $table) {
          $table->foreign('sender')->references('id')->on('users');
          $table->foreign('receiver')->references('id')->on('users');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
