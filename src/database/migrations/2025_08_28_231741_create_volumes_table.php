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
        Schema::create('volumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();

            $table->string('name'); // nome lógico do volume
            $table->enum('type', ['persistent', 'ephemeral']); // tipo do volume
            $table->integer('size')->nullable(); // tamanho em MB/GB (apenas para persistente)
            $table->string('storage_class')->nullable(); // storage class do k8s (apenas persistente)

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volumes');
    }
};
