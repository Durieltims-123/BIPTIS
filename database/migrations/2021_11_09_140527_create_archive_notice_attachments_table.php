<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveNoticeAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
       Schema::create('archive_notice_attachments', function (Blueprint $table) {
         $table->id();
         $table->integer('project_bidder_notice_id');
         $table->string('attachment_name');
         $table->timestamps();
       });

       Schema::table('archive_notice_attachments', function (Blueprint $table) {
         $table->foreign('project_bidder_notice_id')->references('project_bidder_notice_id')->on('project_bidder_notices');
       });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_notice_attachments');
    }
}
