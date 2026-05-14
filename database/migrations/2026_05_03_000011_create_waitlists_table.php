<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // registered accounts only
            $table->foreignId('variant_id')->constrained()->cascadeOnDelete();
            $table->timestamp('notified_at')->nullable(); // set when restock notification is sent
            $table->timestamps();

            $table->unique(['user_id', 'variant_id']);
            $table->index(['variant_id', 'notified_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
