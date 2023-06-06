<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwgEvaluationsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('twg_evaluations', function (Blueprint $table) {
      $table->increments('twg_evaluation_id');
      $table->integer('project_bid')->unsigned();
      $table->enum('twg_evaluation_status',['responsive','non-responsive']);
      $table->string('twg_evaluation_remarks')->nullable();
      $table->date('post_qual_start')->nullable();
      $table->date('post_qual_end')->nullable();
      $table->timestamps();
    });

    Schema::table('twg_evaluations', function (Blueprint $table) {
      $table->foreign('project_bid')->references('project_bid')->on('project_bidders');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('twg_evaluations');
  }
}
