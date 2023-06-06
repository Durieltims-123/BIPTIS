<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeOfAwardsAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('archive_notice_of_awards_attachments', function (Blueprint $table) {
        $table->id();
        $table->integer('notice_award_id');
        $table->string('attachment_name');
        $table->timestamps();
      });

      Schema::table('archive_notice_of_awards_attachments', function (Blueprint $table) {
        $table->foreign('notice_award_id')->references('notice_award_id')->on('notice_of_awards');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_notice_of_awards_attachments');
    }
}
