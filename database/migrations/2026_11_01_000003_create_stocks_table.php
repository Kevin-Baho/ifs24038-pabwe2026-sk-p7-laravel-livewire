<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('price', 12, 2)->nullable();
            $table->unsignedInteger('min_stock')->default(5);
            $table->string('status')->default('available');
            $table->string('cover_image_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
