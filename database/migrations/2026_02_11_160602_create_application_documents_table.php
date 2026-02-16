<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id');
            $table->string('type'); 
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
            
            $table->foreign('application_id')
                  ->references('id')->on('applications')
                  ->onDelete('cascade');
            
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};