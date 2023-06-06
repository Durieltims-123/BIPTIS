<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectLogsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('project_logs', function (Blueprint $table) {
      $table->increments('project_log_id');
      $table->integer('plan_id')->unsigned();
      $table->integer('user_id')->unsigned();
      $table->string('project_log_type');
      $table->string('project_log_remarks')->nullable();
      $table->dateTime('log_date');
      $table->timestamps();
    });

    Schema::table('project_logs', function (Blueprint $table) {
      $table->foreign('plan_id')->references('plan_id')->on('project_plans');
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
    Schema::dropIfExists('project_logs');
  }
}
