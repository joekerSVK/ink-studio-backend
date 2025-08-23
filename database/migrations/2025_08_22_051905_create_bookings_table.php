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
    Schema::create('bookings', function (Blueprint $t) {
        $t->id();
        $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
        $t->foreignId('service_id')->constrained()->cascadeOnDelete();
        $t->date('date');                     // deň rezervácie
        $t->time('start_time');               // začiatok
        $t->time('end_time');                 // automaticky = start + duration
        $t->string('customer_name');
        $t->string('customer_email');
        $t->string('customer_phone')->nullable();
        $t->enum('status',['pending','confirmed','cancelled'])->default('pending');
        $t->text('note')->nullable();
        $t->timestamps();
        // rýchly index na kontrolu kolízií
        $t->index(['artist_id','date','start_time','end_time']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
