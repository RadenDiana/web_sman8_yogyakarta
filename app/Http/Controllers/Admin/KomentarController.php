<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class KomentarController extends Controller
{
    public function index(Request $request)
    {
        $ratings = Rating::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $cari = $request->search;
                $q->where('komentar', 'like', "%{$cari}%");
            })
            ->when($request->sort === 'terlama', fn ($q) => $q->oldest())
            ->when($request->sort !== 'terlama', fn ($q) => $q->latest())
            ->when($request->sort === 'a-z', fn ($q) => $q->orderBy('komentar'))
            ->paginate(10)
            ->withQueryString();

        return view('admin.komentar', compact('ratings'));
    }

    public function destroy(Rating $rating)
    {
        $rating->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate(['ids' => 'required|array|min:1']);
        Rating::whereIn('id', $data['ids'])->delete();

        return back()->with('success', count($data['ids']) . ' komentar berhasil dihapus.');
    }
}