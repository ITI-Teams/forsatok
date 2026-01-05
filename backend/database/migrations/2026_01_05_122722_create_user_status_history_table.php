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
        Schema::create('user_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('User who was affected (null if deleted)');
            $table->string('email');
            $table->string('name');
            $table->string('action'); // 'approved', 'rejected', 'banned', 'unbanned'
            $table->text('reason')->nullable();
            $table->foreignId('actioned_by')->comment('Admin who performed the action');
            $table->timestamps();
            
            $table->index(['user_id', 'action']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_status_history');
    }
};
