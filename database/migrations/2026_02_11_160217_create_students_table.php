<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
           $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            
            // Numéro étudiant unique
            $table->string('student_number')->unique();
            
            // Informations personnelles
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('birth_date');
            $table->string('birth_place')->nullable();
            $table->string('nationality');
            
            // Adresse
            $table->text('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country');
            
            // Informations académiques
            $table->string('last_diploma')->nullable();
            $table->string('institution')->nullable();
            $table->integer('graduation_year')->nullable();
            
            // Photo
            $table->string('photo_path')->nullable();
            
            // Statut
            $table->enum('status', ['active', 'suspended', 'graduated', 'withdrawn'])
                  ->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            
            $table->index('student_number');
            $table->index('email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
