<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfqsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('rfqs', function (Blueprint $table) {
      $table->increments('rfq_id');
      $table->integer('contractor_id')->unsigned();
      $table->date('date_released');
      $table->date('date_received')->nullable();
      $table->string('time_received')->nullable();
      $table->decimal('proposed_bid',12,2)->nullable();
      $table->decimal('bid_as_evaluated',12,2)->nullable();
      $table->timestamps();
    });

    Schema::table('rfqs', function (Blueprint $table) {
      $table->foreign('contractor_id')->references('contractor_id')->on('contractors');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('rfqs');
  }
}
