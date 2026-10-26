<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeminjamanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Peminjaman::latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'judul_buku' => ['required', 'string', 'max:255'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
        ]);

        $peminjaman = Peminjaman::create([
            ...$data,
            'status' => 'dipinjam',
        ]);

        return response()->json($peminjaman, 201);
    }

    public function show(Peminjaman $peminjaman): JsonResponse
    {
        return response()->json($peminjaman);
    }

    public function update(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['dipinjam', 'dikembalikan', 'terlambat']),
            ],
        ]);

        $peminjaman->update($data);

        return response()->json($peminjaman);
    }

    public function destroy(Peminjaman $peminjaman): JsonResponse
    {
        $peminjaman->delete();

        return response()->json(null, 204);
    }
}
