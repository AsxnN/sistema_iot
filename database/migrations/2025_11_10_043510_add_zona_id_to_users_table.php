<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('zona_id')->nullable()->after('email')->constrained('zonas')->onDelete('set null');
            $table->string('telefono')->nullable()->after('zona_id');
            $table->string('rol')->default('operador')->after('telefono'); // operador, admin
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['zona_id']);
            $table->dropColumn(['zona_id', 'telefono', 'rol']);
        });
    }
};
