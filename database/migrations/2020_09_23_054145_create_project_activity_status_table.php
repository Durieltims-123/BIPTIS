<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectActivityStatusTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('project_activity_status', function (Blueprint $table) {
      $table->increments('pro_act_stat_id');
      $table->integer('plan_id')->unsigned();
      $table->integer('procact_id')->unsigned();
      $table->enum('main_status',['pending','completed','rebid','review']);
      $table->enum('pre_proc',['pending','not_needed','finished']);
      $table->enum('advertisement',['pending','not_needed','finished']);
      $table->enum('pre_bid',['pending','not_needed','finished']);
      $table->enum('eligibility_check',['pending','not_needed','finished']);
      $table->enum('open_bid',['pending','not_needed','finished']);
      $table->enum('bid_evaluation',['pending','not_needed','finished']);
      $table->enum('post_qual',['pending','not_needed','finished']);
      $table->enum('award_notice',['pending','not_needed','finished']);
      $table->enum('contract_signing',['pending','not_needed','finished']);
      $table->enum('authority_approval',['pending','not_needed','finished']);
      $table->enum('proceed_notice',['pending','not_needed','finished']);
      $table->timestamps();
    });

    Schema::table('project_activity_status', function (Blueprint $table) {
      $table->foreign('plan_id')->references('plan_id')->on('project_plans');
      $table->foreign('procact_id')->references('procact_id')->on('procacts');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('project_activity_status');
  }
}
