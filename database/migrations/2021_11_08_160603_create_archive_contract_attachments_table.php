<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveContractAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('archive_contract_attachments', function (Blueprint $table) {
        $table->id();
        $table->integer('contract_id');
        $table->string('attachment_name');
        $table->timestamps();
      });

      Schema::table('archive_contract_attachments', function (Blueprint $table) {
        $table->foreign('contract_id')->references('contract_id')->on('contracts');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_contract_attachments');
    }
}
