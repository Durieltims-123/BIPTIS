<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectPlanTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('project_plans', function (Blueprint $table) {
      $table->increments('plan_id');
      $table->string('app_group_no')->nullable();
      $table->string('project_no');
      $table->string('project_title')->nullable();
      $table->year('project_year')->nullable();
      $table->year('year_funded')->nullable();
      $table->enum('project_type',['supplemental','regular']);
      $table->date('date_added')->nullable();
      $table->integer('sector_id')->unsigned()->nullable();
      $table->integer('municipality_id')->unsigned();
      $table->integer('barangay_id')->unsigned();
      $table->integer('projtype_id')->unsigned();
      $table->integer('mode_id')->unsigned();
      $table->integer('fund_id')->unsigned();
      $table->integer('account_id')->unsigned();
      $table->decimal('abc',12,2);
      $table->decimal('project_cost',12,2)->nullable();
      $table->string('abc_post_date')->nullable();
      $table->string('sub_open_date')->nullable();
      $table->string('award_notice_date')->nullable();
      $table->string('contract_signing_date')->nullable();
      $table->enum('status',['pending','onprocess','for_implementation','for_review','for_rebid','completed'])->default('pending');
      $table->integer('re_bid_count')->unsigned();
      $table->boolean('pow_ready')->default(false);
      $table->date('date_pow_added')->nullable();
      $table->date('date_rfq_added')->nullable();
      $table->date('date_itb_added')->nullable();
      $table->integer('governor_id')->unsigned()->nullable();
      $table->integer('project_bid_id')->unsigned()->nullable();
      $table->integer('current_cluster')->unsigned()->nullable();
      $table->integer('latest_procact_id')->unsigned()->nullable();
      $table->decimal('duration',12,2)->nullable();
      $table->string('remarks')->nullable();
      $table->boolean('is_old')->nullable()->default(false);
      $table->timestamps();
    });

    Schema::table('project_plans', function (Blueprint $table) {
      $table->foreign('sector_id')->references('sector_id')->on('sectors');
      $table->foreign('municipality_id')->references('municipality_id')->on('municipalities');
      $table->foreign('barangay_id')->references('barangay_id')->on('barangays');
      $table->foreign('projtype_id')->references('projtype_id')->on('projtypes');
      $table->foreign('mode_id')->references('mode_id')->on('procurement_modes');
      $table->foreign('fund_id')->references('fund_id')->on('funds');
      $table->foreign('account_id')->references('account_id')->on('account_classifications');
      $table->foreign('governor_id')->references('governor_id')->on('governors');
    });
  }



  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('project_plans');
  }
}
