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
        Schema::table('job_posts', function (Blueprint $table) {
            $table->text('responsibilities');
            $table->text('qualification');
            $table->text('benefits');
            $table->enum('work_type',['part-time','full-time','freelance'])->default('full-time');
            $table->enum('work_place',['hybrid','remote','on-site'])->default('on-site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            //
        });
    }
};
