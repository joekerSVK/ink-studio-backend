<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
    protected $fillable = [
        'artist_id','service_id','date','start_time','end_time',
        'customer_name','customer_email','customer_phone','status','note'
    ];
    protected $casts = ['date'=>'date'];
    public function artist(){ return $this->belongsTo(Artist::class); }
    public function service(){ return $this->belongsTo(Service::class); }
}
