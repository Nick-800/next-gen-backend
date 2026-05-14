<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1–5
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // One review per user per variant
            $table->unique(['user_id', 'variant_id']);
            $table->index(['variant_id', 'approved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
