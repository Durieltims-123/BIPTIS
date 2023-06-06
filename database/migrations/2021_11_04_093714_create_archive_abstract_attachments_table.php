<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveAbstractAttachmentsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('archive_abstract_attachments', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('archive_abstract_id')->unsigned();
      $table->string('attachment_name');
      $table->timestamps();
    });

    Schema::table('archive_abstract_attachments', function (Blueprint $table) {
      $table->foreign('archive_abstract_id')->references('id')->on('archive_abstracts');
    });


  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('archive_abstract_attachments');
  }
}
