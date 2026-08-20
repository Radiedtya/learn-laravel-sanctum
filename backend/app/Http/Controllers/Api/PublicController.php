<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PublicController extends Controller
{
    public function films()
    {
        try {

            $films = DB::table('films')
                ->join('genres', 'films.genre_id', '=', 'genres.id')
                ->select(
                    'films.id',
                    'films.judul_film',
                    'films.slug',
                    'films.poster',
                    'films.tahun_rilis',
                    'films.durasi',
                    'films.sutradara',
                    'genres.nama_genre'
                )
                ->orderBy('films.id', 'asc')
                ->paginate(10);


            if (!$films) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film tidak ditemukan.'
                ], 404);
            }
                
            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil diambil.',
                'data' => $films
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function detailFilm(Request $request)
    {
        try {

            $film = DB::table('films')
                ->join('genres', 'films.genre_id', '=', 'genres.id')
                ->select(
                    'films.*',
                    'genres.nama_genre'
                )
                ->where('films.id', $request->id)
                ->first();

            if (!$film) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film tidak ditemukan.'
                ], 404);
            }

            $actors = DB::table('aktor_film')
                ->join('aktors', 'aktor_film.aktor_id', '=', 'aktors.id')
                ->where('aktor_film.film_id', $request->id)
                ->select(
                    'aktors.id',
                    'aktors.nama_aktor'
                )
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Detail film berhasil diambil.',
                'film' => $film,
                'actors' => $actors
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function genres()
    {
        try {

            $genres = DB::table('genres')
                ->select(
                    'id',
                    'nama_genre',
                    'slug'
                )
                ->orderBy('nama_genre', 'asc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data genre berhasil diambil.',
                'data' => $genres
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function filmByGenre(Request $request)
    {
        try {

            $genre = DB::table('genres')
                ->where('id', $request->id)
                ->first();

            if (!$genre) {

                return response()->json([
                    'status' => false,
                    'message' => 'Genre tidak ditemukan.'
                ], 404);

            }

            $films = DB::table('films')
                ->join('genres', 'films.genre_id', '=', 'genres.id')
                ->where('genres.id', $request->id)
                ->select(
                    'films.id',
                    'films.judul_film',
                    'films.slug',
                    'films.poster',
                    'films.tahun_rilis',
                    'films.durasi',
                    'films.sutradara',
                    'genres.nama_genre'
                )
                ->paginate(10);

            return response()->json([
                'status' => true,
                'genre' => $genre,
                'data' => $films
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function actors()
    {
        try {

            $actors = DB::table('aktors')
                ->select(
                    'id',
                    'nama_aktor',
                    'umur',
                    'gender',
                    'foto',
                )
                ->orderBy('nama_aktor', 'asc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data aktor berhasil diambil.',
                'data' => $actors
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function filmByActor(Request $request)
    {
        try {

            $actor = DB::table('aktors')
                ->where('id', $request->id)
                ->first();

            if (!$actor) {

                return response()->json([
                    'status' => false,
                    'message' => 'Aktor tidak ditemukan.'
                ], 404);

            }

            $films = DB::table('aktor_film')
                ->join('films', 'aktor_film.film_id', '=', 'films.id')
                ->join('genres', 'films.genre_id', '=', 'genres.id')
                ->join('aktors', 'aktor_film.aktor_id', '=', 'aktors.id')
                ->where('aktors.id', $request->id)
                ->select(
                    'films.id',
                    'films.judul_film',
                    'films.slug',
                    'films.poster',
                    'films.tahun_rilis',
                    'films.durasi',
                    'films.sutradara',
                    'genres.nama_genre'
                )
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data aktor berhasil diambil.',
                'actor' => $actor,
                'data' => $films
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'keyword' => 'required|string|min:2'
            ]);

            $keyword = $request->keyword;

            $films = DB::table('films')
                ->join('genres', 'films.genre_id', '=', 'genres.id')
                ->select(
                    'films.id',
                    'films.judul_film',
                    'films.slug',
                    'films.poster',
                    'films.tahun_rilis',
                    'films.durasi',
                    'films.sutradara',
                    'genres.nama_genre'
                )
                ->where('films.judul_film', 'like', '%' . $keyword . '%')
                ->orderBy('films.judul_film', 'asc')
                ->paginate(10);

            if ($films->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Film tidak ditemukan.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data film berhasil ditemukan.',
                'data' => $films
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
