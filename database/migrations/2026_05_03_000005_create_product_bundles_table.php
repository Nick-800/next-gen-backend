<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bundled_variant_id')->constrained('variants')->cascadeOnDelete();
            $table->primary(['product_id', 'bundled_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bundles');
    }
};
