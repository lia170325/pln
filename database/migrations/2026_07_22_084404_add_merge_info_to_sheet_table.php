<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sheet', function (Blueprint $table) {

            $table->longText('merge_cells')->nullable()->after('total_rows');

            $table->integer('highest_row')->nullable()->after('merge_cells');

            $table->integer('highest_column')->nullable()->after('highest_row');

        });
    }

    public function down(): void
    {
        Schema::table('sheet', function (Blueprint $table) {

            $table->dropColumn([
                'merge_cells',
                'highest_row',
                'highest_column'
            ]);

        });
    }
};