<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilmController extends Controller
{
    public function index() {
        try {
            $films = Film::latest()->get();

            if ($films->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Film tidak ada',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data Film Berhasil diambil',
                'data'    => $films
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request) {
        try {
            
            $request->validate([
                'judul_film' => 'required|string|max:255|unique:films,judul_film',
                'durasi'     => 'required|integer|min:1',
                'rating'     => 'required|numeric|min:0|max:5',
                'deskripsi'  => 'nullable|string',
                'tahun_rilis'=> 'required|integer|digits:4|min:1900|max:'.date('Y'),
                'poster'     => 'required|string|max:255',
                'genre_id'   => 'required|integer|exists:genres,id',
                'sutradara'  => 'required|string|max:255',
            ]);

            $film = Film::create([
                'judul_film' => $request->judul_film,
                'slug'       => Str::slug($request->judul_film) . Str::random(10),
                'durasi'     => $request->durasi,
                'rating'     => $request->rating,
                'deskripsi'  => $request->deskripsi,
                'tahun_rilis'=> $request->tahun_rilis,
                'poster'     => $request->poster,
                'genre_id'   => $request->genre_id,
                'sutradara'  => $request->sutradara,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data Film Berhasil ditambahkan',
                'data'    => $film
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id) {
        try {

            $film = Film::find($id);

            if (!$film) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Film tidak ditemukan',
                ], 404);
            }
            
            $request->validate([
                'judul_film' => 'required|string|max:255|unique:films,judul_film,' . $id,
                'durasi'     => 'required|integer|min:1',
                'rating'     => 'required|numeric|min:0|max:5',
                'deskripsi'  => 'nullable|string',
                'tahun_rilis'=> 'required|integer|digits:4|min:1900|max:'.date('Y'),
                'poster'     => 'required|string|max:255',
                'genre_id'   => 'required|integer|exists:genres,id',
                'sutradara'  => 'required|string|max:255',
            ]);
            
            $film->update([
                'judul_film' => $request->judul_film,
                'slug'       => Str::slug($request->judul_film) . Str::random(10),
                'durasi'     => $request->durasi,
                'rating'     => $request->rating,
                'deskripsi'  => $request->deskripsi,
                'tahun_rilis'=> $request->tahun_rilis,
                'poster'     => $request->poster,
                'genre_id'   => $request->genre_id,
                'sutradara'  => $request->sutradara,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data Film Berhasil diubah',
                'data'    => $film
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id) {
        try {

            $film = Film::find($id);

            if (!$film) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Film tidak ditemukan',
                ], 404);
            }

            $film->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data Film Berhasil dihapus',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}