<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procurement_processes_id');
            $table->unsignedBigInteger('document_types_id');
            $table->string('process_document_status');
            $table->timestamps();
            
            $table->index('procurement_processes_id');
            $table->index('document_types_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_documents');
    }
}
