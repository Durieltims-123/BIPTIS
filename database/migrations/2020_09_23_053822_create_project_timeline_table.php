<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTimelineTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('project_timelines', function (Blueprint $table) {
      $table->increments('timeline_id');
      $table->integer('plan_id')->unsigned();
      $table->integer('procact_id')->unsigned();
      $table->enum('timeline_status',['set','pending'])->default('pending');
      $table->date('pre_proc_date')->nullable();
      $table->date('advertisement_start')->nullable();
      $table->date('advertisement_end')->nullable();
      $table->date('pre_bid_start')->nullable();
      $table->date('pre_bid_end')->nullable();
      $table->date('bid_submission_start')->nullable();
      $table->date('bid_submission_end')->nullable();
      $table->date('bid_evaluation_start')->nullable();
      $table->date('bid_evaluation_end')->nullable();
      $table->date('post_qualification_start')->nullable();
      $table->date('post_qualification_end')->nullable();
      $table->date('award_notice_start')->nullable();
      $table->date('award_notice_end')->nullable();
      $table->date('contract_signing_start')->nullable();
      $table->date('contract_signing_end')->nullable();
      $table->date('authority_approval_start')->nullable();
      $table->date('authority_approval_end')->nullable();
      $table->date('proceed_notice_start')->nullable();
      $table->date('proceed_notice_end')->nullable();
      $table->timestamps();
    });

    Schema::table('project_timelines', function (Blueprint $table) {
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
    Schema::dropIfExists('project_timelines');
  }
}
