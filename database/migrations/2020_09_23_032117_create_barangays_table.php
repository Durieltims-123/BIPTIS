<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangaysTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('barangays', function (Blueprint $table) {
      $table->increments('barangay_id');
      $table->string('barangay_code',11);
      $table->string('barangay_name');
      $table->integer('municipality_id')->unsigned();
      $table->timestamps();
    });

    Schema::table('barangays', function (Blueprint $table) {
      $table->foreign('municipality_id')->references('municipality_id')->on('municipalities');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('barangays');
  }
}
