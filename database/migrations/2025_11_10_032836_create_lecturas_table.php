<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispositivo_id')->constrained()->onDelete('cascade');
            $table->integer('nivel'); // 0, 1, 2, 3
            $table->decimal('temperatura', 5, 2);
            $table->string('estado_temp')->nullable(); // NUEVO
            $table->decimal('humedad', 5, 2);
            $table->integer('tds');
            $table->string('estado_tds')->nullable(); // NUEVO
            $table->bigInteger('timestamp')->nullable();
            $table->integer('rssi')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
            
            $table->index('dispositivo_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturas');
    }
};
