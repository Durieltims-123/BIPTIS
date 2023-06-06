<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_logs', function (Blueprint $table) {
          $table->id();
          $table->string('log_group')->nullable();
          $table->string('activity');
          $table->unsignedBigInteger('project_document_id');
          $table->string('log_type')->nullable();
          $table->timestamps();

          $table->index('project_document_id');
        });
        /*
        Schema::table('document_logs', function (Blueprint $table) {
          $table->foreign('project_document_id')->references('id')->on('project_documents');
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_logs');
    }
}
