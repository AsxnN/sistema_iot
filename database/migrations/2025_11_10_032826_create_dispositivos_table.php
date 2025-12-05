<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->string('ip_actual')->nullable();
            $table->integer('rssi_actual')->nullable();
            $table->timestamp('ultima_comunicacion')->nullable();
            $table->timestamps();
            
            $table->index('zona_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};
