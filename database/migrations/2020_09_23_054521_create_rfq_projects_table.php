<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfqProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfq_projects', function (Blueprint $table) {
            $table->increments('rfq_project_id');
            $table->integer('rfq_id')->unsigned();
            $table->integer('procact_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('rfq_projects', function (Blueprint $table) {
          $table->foreign('rfq_id')->references('rfq_id')->on('rfqs');
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
        Schema::dropIfExists('rfq_projects');
    }
}
