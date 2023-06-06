<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveAbstractTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('archive_abstracts', function (Blueprint $table) {
      $table->id();
      $table->date('date_opened');
      $table->integer('updated_by')->unsigned();
      $table->integer('deleted_by')->unsigned()->nullable();
      $table->integer('deleted')->nullable();
      $table->timestamp('deleted_at')->nullable();
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
    Schema::dropIfExists('archive_abstracts');
  }
}
