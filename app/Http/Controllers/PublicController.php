<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\Service;
use App\Models\PortfolioItem;

class PublicController extends Controller
{
    public function ping()     { return response()->json(['message'=>'API is working']); }
    public function artists()  { return Artist::orderBy('name')->get(); }
    public function services() { return Service::orderBy('duration_minutes')->get(); }
    public function portfolio(Request $r) {
        $q = PortfolioItem::with('artist')->latest();
        if ($slug = $r->query('artist_slug')) {
            $artistId = Artist::where('slug',$slug)->value('id');
            if ($artistId) $q->where('artist_id',$artistId);
        }
        return $q->paginate(12);
    }
    public function contact(Request $r)
{
    $data = $r->validate([
        'name'=>'required|string|max:120',
        'email'=>'required|email',
        'message'=>'required|string|max:4000',
    ]);
    // jednoduché logovanie (alebo pošli mail podľa konfigurácie)
    \Log::info('CONTACT_FORM', $data);
    return response()->json(['ok'=>true]);
}

}
