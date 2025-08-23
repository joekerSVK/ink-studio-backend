<?php

namespace App\Http\Controllers;

use App\Models\{Artist, Service, Availability, Booking};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    // Vráti dostupné TIME SLOTS pre daný deň
    public function availability(Request $r) {
        $data = $r->validate([
            'artist_id'  => ['required','exists:artists,id'],
            'service_id' => ['required','exists:services,id'],
            'date'       => ['required','date'] // YYYY-MM-DD
        ]);

        $service = Service::findOrFail($data['service_id']);
        $artist  = Artist::findOrFail($data['artist_id']);
        $date    = Carbon::parse($data['date']);
        $weekday = (int)$date->isoWeekday(); // 1..7

        $avail = Availability::where('artist_id',$artist->id)
            ->where('weekday',$weekday)->get();

        $duration = $service->duration_minutes;

        // už zarezervované sloty v ten deň
        $busy = Booking::where('artist_id',$artist->id)
            ->whereDate('date',$date)->whereIn('status',['pending','confirmed'])
            ->get(['start_time','end_time']);

        $slots = [];
        foreach($avail as $a){
            $start = Carbon::parse($a->start_time);
            $end   = Carbon::parse($a->end_time);
            for ($t = $start->copy(); $t->addMinutes(0)->lte($end->copy()->subMinutes($duration)); $t->addMinutes(30)) {
                $slotStart = $t->copy();
                $slotEnd   = $t->copy()->addMinutes($duration);

                // kolízia s busy?
                $conflict = $busy->first(function($b) use($slotStart,$slotEnd){
                    $bs = Carbon::parse($b->start_time);
                    $be = Carbon::parse($b->end_time);
                    return $slotStart < $be && $slotEnd > $bs; // overlap
                });

                if (!$conflict && $slotEnd <= Carbon::parse($a->end_time)) {
                    $slots[] = [
                        'start' => $slotStart->format('H:i'),
                        'end'   => $slotEnd->format('H:i'),
                    ];
                }
            }
        }
        return array_values($slots);
    }

    // Vytvorenie rezervácie s kontrolou kolízií
    public function store(Request $r) {
        $data = $r->validate([
            'artist_id'       => ['required','exists:artists,id'],
            'service_id'      => ['required','exists:services,id'],
            'date'            => ['required','date'],
            'start_time'      => ['required'], // 'HH:MM'
            'customer_name'   => ['required','string','max:120'],
            'customer_email'  => ['required','email'],
            'customer_phone'  => ['nullable','string','max:40'],
            'note'            => ['nullable','string']
        ]);

        $service = Service::find($data['service_id']);
        $start = Carbon::parse($data['date'].' '.$data['start_time']);
        $end   = $start->copy()->addMinutes($service->duration_minutes);

        // kolízia?
        $overlap = Booking::where('artist_id',$data['artist_id'])
            ->whereDate('date',$start->toDateString())
            ->whereIn('status',['pending','confirmed'])
            ->where(function($q) use($start,$end){
                $q->whereBetween('start_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                  ->orWhereBetween('end_time',   [$start->format('H:i:s'), $end->format('H:i:s')])
                  ->orWhere(function($qq) use($start,$end){
                      $qq->where('start_time','<=',$start->format('H:i:s'))
                         ->where('end_time','>=',$end->format('H:i:s'));
                  });
            })->exists();

        if ($overlap) {
            return response()->json(['message'=>'Termín je už obsadený.'], 422);
        }

        $booking = Booking::create([
            ...$data,
            'end_time' => $end->format('H:i:s'),
            'status'   => 'pending'
        ]);

        return response()->json($booking, 201);
    }

    public function show($id)    { return Booking::with(['artist','service'])->findOrFail($id); }
    public function cancel($id)  {
        $b = Booking::findOrFail($id);
        $b->status = 'cancelled'; $b->save();
        return $b;
    }
}

