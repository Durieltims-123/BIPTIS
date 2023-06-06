<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDocumentsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('project_documents', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('plan_id');
        $table->unsignedBigInteger('document_type_id');
        $table->unsignedBigInteger('contractor_id');
        $table->unsignedBigInteger('procurement_processes_id');
        $table->unsignedBigInteger('sender');
        $table->unsignedBigInteger('receiver');
        $table->string('status')->nullable();
        $table->string('active_status')->nullable();
        $table->string('file_status')->nullable();
        $table->string('file_directory')->nullable();
        $table->string('batch_remarks')->nullable();
        $table->string('remarks')->nullable();
        $table->timestamps();

        $table->index('plan_id');
        $table->index('document_type_id');
        $table->index('contractor_id');
        $table->index('sender');
        $table->index('receiver');
    });
    /*
    Schema::table('project_documents', function (Blueprint $table) {
        $table->foreign('plan_id')->references('plan_id')->on('project_plans');
        $table->foreign('contractor_id')->references('contractor_id')->on('contractors');
        $table->foreign('document_type_id')->references('id')->on('document_types');
        $table->foreign('sender')->references('id')->on('users');
        $table->foreign('receiver')->references('id')->on('users');
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
    Schema::dropIfExists('project_document');
  }
}
