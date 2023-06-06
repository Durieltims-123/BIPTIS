<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveAppsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('archive_apps', function (Blueprint $table) {
      $table->increments('id');
      $table->year('project_year');
      $table->string('app_group_no')->nullable();
      $table->string('project_type');
      $table->integer('fund_category_id')->nullable();
      $table->text('remarks')->nullable();
      $table->timestamps();
    });

    Schema::table('archive_apps', function (Blueprint $table) {
      $table->foreign('fund_category_id')->references('fund_category_id')->on('fund_category');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('archive_apps');
  }
}
