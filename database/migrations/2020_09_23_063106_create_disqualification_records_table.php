<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisqualificationRecordsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('disqualification_records', function (Blueprint $table) {
      $table->increments('record_id');
      $table->integer('project_bid')->unsigned();
      $table->integer('user_id')->unsigned();
      $table->string('remarks');
      $table->timestamps();
    });

    Schema::table('disqualification_records', function (Blueprint $table) {
      $table->foreign('project_bid')->references('project_bid')->on('project_bidders');
      $table->foreign('user_id')->references('id')->on('users');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('disqualification_records');
  }
}
