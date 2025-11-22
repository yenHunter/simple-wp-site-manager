<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('domain_name');
            $table->integer('port'); // The Host Port (e.g., 8001)
            
            // Docker Info
            $table->string('container_name')->unique();
            $table->string('container_id')->nullable();
            
            // WP Database Info
            $table->string('db_name');
            $table->string('db_user');
            $table->text('db_password'); // Encrypted
            
            $table->string('status')->default('deploying'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};