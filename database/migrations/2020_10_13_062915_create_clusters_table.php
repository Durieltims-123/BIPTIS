<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClustersTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('clusters', function (Blueprint $table) {
      $table->increments('cluster_id');
      $table->integer('cluster_mode')->unsigned();
      $table->date('date_opened')->nullable();
      $table->timestamps();
    });

    Schema::create('clustered_project_plans', function (Blueprint $table) {
      $table->id();
      $table->integer('cluster_id')->unsigned();
      $table->integer('plan_id')->unsigned();
      $table->timestamps();
    });

    Schema::table('clusters', function (Blueprint $table) {
      $table->foreign('cluster_mode')->references('mode_id')->on('procurement_modes');
    });

    Schema::table('clustered_project_plans', function (Blueprint $table) {
      $table->foreign('plan_id')->references('plan_id')->on('project_plans');
      $table->foreign('cluster_id')->references('cluster_id')->on('clusters');
    });

    Schema::table('project_plans', function (Blueprint $table) {
      $table->foreign('current_cluster')->references('cluster_id')->on('clusters');
    });

    Schema::table('procacts', function (Blueprint $table) {
      $table->foreign('cluster_id')->references('cluster_id')->on('clusters');
    });





  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('clusters');
  }
}
