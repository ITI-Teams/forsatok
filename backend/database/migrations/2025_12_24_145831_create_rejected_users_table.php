<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rejected_users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index(); // Keep email for history
            $table->string('name');
            $table->string('type')->nullable(); // employer/candidate etc.
            $table->text('rejection_reason')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rejected_users');
    }
};
