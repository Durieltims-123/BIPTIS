<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('contractors', function (Blueprint $table) {
      $table->increments('contractor_id');
      $table->string('business_name');
      $table->string('owner');
      $table->string('position')->default("Manager");
      $table->string('address');
      $table->string('contact_number');
      $table->enum('status',['active','inactive'])->default('active');
      $table->timestamps();
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('contractors');
  }
}
