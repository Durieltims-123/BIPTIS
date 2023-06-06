<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveNoticeToProceedAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
       Schema::create('archive_notice_to_proceed_attachments', function (Blueprint $table) {
         $table->id();
         $table->integer('ntp_id');
         $table->string('attachment_name');
         $table->timestamps();
       });

       Schema::table('archive_notice_to_proceed_attachments', function (Blueprint $table) {
         $table->foreign('ntp_id')->references('ntp_id')->on('notice_to_proceeds');
       });
     }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_notice_to_proceed_attachments');
    }
}
