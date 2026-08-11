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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('lokasi', ['Kantor Pusat', 'Kantor Perwakilan'])->default('Kantor Pusat')->after('role');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('bisa_remote')->default(false)->after('detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('bisa_remote');
        });
    }
};
