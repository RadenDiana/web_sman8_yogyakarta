<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;

class EkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        $ekstrakurikuler = Ekstrakurikuler::query()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('nama', 'like', '%' . $request->search . '%'))
            ->when($request->sort === 'a-z',     fn ($q) => $q->orderBy('nama'))
            ->when($request->sort === 'z-a',     fn ($q) => $q->orderByDesc('nama'))
            ->when($request->sort === 'terlama', fn ($q) => $q->oldest())
            ->when(! in_array($request->sort, ['a-z', 'z-a', 'terlama']), fn ($q) => $q->latest())
            ->paginate(10)
            ->withQueryString();

        return view('admin.ekstrakurikuler', compact('ekstrakurikuler'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|max:255']);

        Ekstrakurikuler::create($request->only('nama'));

        return back()->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $request->validate(['nama' => 'required|max:255']);

        $ekstrakurikuler->update($request->only('nama'));

        return back()->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->delete();

        return back()->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}