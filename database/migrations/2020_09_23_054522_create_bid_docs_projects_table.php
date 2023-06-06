<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidDocsProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bid_doc_projects', function (Blueprint $table) {
            $table->increments('bid_doc_project_id');
            $table->integer('bid_doc_id')->unsigned();
            $table->integer('procact_id')->unsigned();
            $table->timestamps();

        });

        Schema::table('bid_doc_projects', function (Blueprint $table) {
          $table->foreign('bid_doc_id')->references('bid_doc_id')->on('bid_docs');
          $table->foreign('procact_id')->references('procact_id')->on('procacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bid_doc_projects');
    }
}
