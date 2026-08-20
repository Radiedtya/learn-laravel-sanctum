<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aktor;
use Exception;
use Illuminate\Http\Request;

class AktorController extends Controller
{
    public function index()
    {
        try {
            $aktors = Aktor::latest()->get();

            if ($aktors->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Aktor tidak ada',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data Aktor Berhasil diambil',
                'data'    => $aktors
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_aktor' => 'required|string|max:255|unique:aktors,nama_aktor',
                'gender'     => 'required|in:Laki-laki,Perempuan',
                'umur'       => 'required|integer',
                'foto'       => 'required|string|max:255',
            ]);

            $aktor = Aktor::create($request->all());

            return response()->json([
                'status'  => true,
                'message' => 'Data Aktor Berhasil ditambahkan',
                'data'    => $aktor
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $aktor = Aktor::find($id);

            if (!$aktor) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Aktor tidak ditemukan',
                ], 404);
            }

            $request->validate([
                'nama_aktor' => 'required|string|max:255|unique:aktors,nama_aktor,' . $id,
                'gender'     => 'required|in:Laki-laki,Perempuan',
                'umur'       => 'required|integer',
                'foto'       => 'required|string|max:255',
            ]);

            $aktor->update($request->all());

            return response()->json([
                'status'  => true,
                'message' => 'Data Aktor Berhasil diubah',
                'data'    => $aktor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $aktor = Aktor::find($id);

            if (!$aktor) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Aktor tidak ditemukan',
                ], 404);
            }

            $aktor->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data Aktor Berhasil dihapus',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}