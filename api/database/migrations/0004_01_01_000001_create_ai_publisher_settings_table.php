<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_publisher_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained('tenants')->cascadeOnDelete();
            $table->string('company')->nullable();
            $table->json('audience')->nullable();
            $table->string('tone')->nullable()->default('profissional');
            $table->json('content_rules')->nullable();
            $table->string('default_status', 30)->nullable()->default('draft');
            $table->unsignedInteger('min_content_length')->nullable()->default(200);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_publisher_settings');
    }
};
