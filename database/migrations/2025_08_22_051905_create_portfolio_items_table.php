<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void {
    Schema::create('portfolio_items', function (Blueprint $t) {
        $t->id();
        $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
        $t->string('image_url');
        $t->string('title')->nullable();
        $t->text('description')->nullable();
        $t->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
