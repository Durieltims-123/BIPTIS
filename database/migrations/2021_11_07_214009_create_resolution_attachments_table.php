<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResolutionAttachmentsTable extends Migration
{

    public function up()
    {
      Schema::create('archive_resolution_attachments', function (Blueprint $table) {
        $table->id();
        $table->integer('resolution_id')->unsigned();
        $table->string('attachment_name');
        $table->timestamps();
      });

      Schema::table('archive_resolution_attachments', function (Blueprint $table) {
        $table->foreign('resolution_id')->references('resolution_id')->on('resolutions');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resolution_attachments');
    }
}
