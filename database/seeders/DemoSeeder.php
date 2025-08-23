<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
    $a1 = \App\Models\Artist::create(['name'=>'Marek', 'slug'=>'marek','specialty'=>'blackwork']);
    $a2 = \App\Models\Artist::create(['name'=>'Sára',  'slug'=>'sara','specialty'=>'realism']);

    \App\Models\Service::insert([
      ['name'=>'Malý tattoo (do 5cm)','duration_minutes'=>60,'price_eur'=>80],
      ['name'=>'Stredný (5–10cm)','duration_minutes'=>120,'price_eur'=>150],
    ]);

    // dostupnosť Po–Pia 10:00–18:00
    foreach([1,2,3,4,5] as $w){
      \App\Models\Availability::create(['artist_id'=>$a1->id,'weekday'=>$w,'start_time'=>'10:00','end_time'=>'18:00']);
      \App\Models\Availability::create(['artist_id'=>$a2->id,'weekday'=>$w,'start_time'=>'10:00','end_time'=>'18:00']);
    }
}
}
