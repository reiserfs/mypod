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
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();

            $table->string('name');             // nome da rede/ingress
            $table->string('host')->nullable(); // ex: app.example.com
            $table->integer('port')->nullable(); 
            $table->string('protocol')->default('http'); // http, https, tcp
            $table->string('path')->nullable(); // ex: /api
            $table->string('service_name')->nullable(); 
            $table->string('pathType')->default('Prefix'); // Prefix, Exact, ImplementationSpecific
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
