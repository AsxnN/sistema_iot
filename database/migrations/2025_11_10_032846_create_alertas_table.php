<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->onDelete('cascade');
            $table->foreignId('lectura_id')->nullable()->constrained('lecturas')->onDelete('set null');
            $table->string('tipo'); // critico, alto, medio
            $table->integer('nivel');
            $table->text('mensaje');
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_en')->nullable();
            $table->foreignId('leida_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('tipo');
            $table->index('leida');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
