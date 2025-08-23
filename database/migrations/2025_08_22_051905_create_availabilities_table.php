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
    Schema::create('availabilities', function (Blueprint $t) {
        $t->id();
        $t->foreignId('artist_id')->constrained()->cascadeOnDelete();
        $t->unsignedTinyInteger('weekday');   // 1=Mon ... 7=Sun
        $t->time('start_time');               // napr. 10:00
        $t->time('end_time');                 // napr. 18:00
        $t->timestamps();
        $t->unique(['artist_id','weekday','start_time','end_time']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
