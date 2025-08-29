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
        Schema::create('csv_export_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('export_id')->unique();
            $table->string('model');
            $table->string('file_path')->nullable();
            $table->string('filename')->nullable();
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])
                  ->default('pending');
            $table->json('columns')->nullable();
            $table->json('filters')->nullable();
            $table->json('options')->nullable();
            $table->string('format')->default('csv');
            $table->integer('user_id')->nullable();
            $table->string('tenant_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('user_id');
            $table->index('tenant_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_export_jobs');
    }
};