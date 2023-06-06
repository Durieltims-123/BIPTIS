<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolutionsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('resolutions', function (Blueprint $table) {
      $table->increments('resolution_id');
      $table->string('resolution_number');
      $table->date('resolution_date');
      $table->date('date_opened');
      $table->enum('type',["RRA","RPD"]);
      $table->integer('with_attachment');
      $table->integer('governor_id')->unsigned();
      $table->timestamps();
    });


    Schema::table('resolutions', function (Blueprint $table) {
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
    Schema::dropIfExists('resolutions');
  }
}
