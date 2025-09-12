<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\Laravel\Models\MoonshineUserRole;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('valor', 8, 2);
            $table->text('descricao')->nullable();
            $table->integer('max_containers');
            $table->integer('max_memoria');
            $table->integer('max_disco');
            $table->integer('max_cpu');
            $table->timestamps();
        });

        DB::table('planos')->insert([
            'id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            'name' => 'Max',
            'descricao' => 'Teste',
            'valor' => 29.99,
            'max_containers' => 100,
            'max_memoria' => 2048,
            'max_disco' => 2048,
            'max_cpu' => 6            
        ]);          

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
