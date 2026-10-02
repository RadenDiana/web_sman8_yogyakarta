<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validateWithBag('rating', [
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        Rating::create($request->only('rating', 'komentar'));

        return redirect(route('home') . '#rating')->with('ratingTerkirim', true);
    }
}