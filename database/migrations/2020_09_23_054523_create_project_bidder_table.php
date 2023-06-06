<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectBidderTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('project_bidders', function (Blueprint $table) {
      $table->increments('project_bid');
      $table->integer('rfq_project_id')->unsigned()->nullable();
      $table->integer('bid_doc_project_id')->unsigned()->nullable();
      $table->enum('bid_status',['active','late','responsive','disqualified','non-responsive']);
      $table->timestamps();
    });

    Schema::table('project_bidders', function (Blueprint $table) {
      $table->foreign('rfq_project_id')->references('rfq_project_id')->on('rfq_projects');
      $table->foreign('bid_doc_project_id')->references('bid_doc_project_id')->on('bid_doc_projects');
    });

    Schema::table('project_plans', function (Blueprint $table) {
      $table->foreign('project_bid_id')->references('project_bid')->on('project_bidders');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('project_bidders');
  }
}
