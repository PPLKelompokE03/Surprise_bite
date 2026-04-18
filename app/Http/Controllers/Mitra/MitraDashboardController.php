<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MitraDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $restaurants = Restaurant::where('user_id', $request->user()->id)->get();
        return view('mitra.dashboard', compact('restaurants'));
    }
}
