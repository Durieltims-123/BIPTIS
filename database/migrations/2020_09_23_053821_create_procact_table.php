<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcactTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('procacts', function (Blueprint $table) {
      $table->increments('procact_id');
      $table->integer('plan_id')->unsigned();
      $table->integer('cluster_id')->unsigned();
      $table->date('pre_proc')->nullable();
      $table->date('advertisement')->nullable();
      $table->date('pre_bid')->nullable();
      $table->date('eligibility_check')->nullable();
      $table->date('open_bid')->nullable();
      $table->string('open_time')->nullable();
      $table->date('bid_evaluation')->nullable();
      $table->date('post_qual')->nullable();
      $table->date('award_notice')->nullable();
      $table->date('contract_signing')->nullable();
      $table->date('authority_approval')->nullable();
      $table->date('proceed_notice')->nullable();
      $table->timestamps();
    });

    Schema::table('procacts', function (Blueprint $table) {
      $table->foreign('plan_id')->references('plan_id')->on('project_plans');
    });

    Schema::table('project_plans', function (Blueprint $table) {
      $table->foreign('latest_procact_id')->references('procact_id')->on('procacts');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('procact');
  }
}
