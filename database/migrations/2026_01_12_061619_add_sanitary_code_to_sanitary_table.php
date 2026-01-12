<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sanitary', function (Blueprint $table) {
            $table->string('sanitary_code')
                  ->nullable()
                  ->after('id'); // position is optional
        });
    }

    public function down(): void
    {
        Schema::table('sanitary', function (Blueprint $table) {
            $table->dropColumn('sanitary_code');
        });
    }
};
