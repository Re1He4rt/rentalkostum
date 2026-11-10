<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKostumRequest;
use App\Models\Kategori;
use App\Models\Kostum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KostumController extends Controller
{
    public function index(Request $request)
    {
        $kostum = Kostum::with('kategori')
            ->when($request->input('q'), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', "%{$q}%")
                        ->orWhere('kode', 'like', "%{$q}%");
                });
            })
            ->when($request->input('kategori_id'), fn ($query, $id) => $query->where('kategori_id', $id))
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kategori = Kategori::orderBy('nama')->get();

        return view('kostum.index', compact('kostum', 'kategori'));
    }

    public function create()
    {
        $kategori = Kategori::orderBy('nama')->get();

        return view('kostum.create', compact('kategori'));
    }

    public function store(StoreKostumRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('kostum', 'public');
        }

        Kostum::create($data);

        return redirect()->route('kostum.index')->with('success', 'Kostum berhasil ditambahkan.');
    }

    public function show(Kostum $kostum)
    {
        $kostum->load('kategori');

        return view('kostum.show', compact('kostum'));
    }

    public function edit(Kostum $kostum)
    {
        $kategori = Kategori::orderBy('nama')->get();

        return view('kostum.edit', compact('kostum', 'kategori'));
    }

    public function update(StoreKostumRequest $request, Kostum $kostum)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($kostum->gambar) {
                Storage::disk('public')->delete($kostum->gambar);
            }

            $data['gambar'] = $request->file('gambar')->store('kostum', 'public');
        }

        $kostum->update($data);

        return redirect()->route('kostum.index')->with('success', 'Kostum berhasil diperbarui.');
    }

    public function destroy(Kostum $kostum)
    {
        if ($kostum->gambar) {
            Storage::disk('public')->delete($kostum->gambar);
        }

        $kostum->delete();

        return redirect()->route('kostum.index')->with('success', 'Kostum berhasil dihapus.');
    }
}
