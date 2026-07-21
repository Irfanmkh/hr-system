<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Candidate::query();

        // search

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('name ILIKE ?', ["%{$search}%"])
                  ->orWhereRaw('email ILIKE ?', ["%{$search}%"])
                  ->orWhereRaw('phone ILIKE ?', ["%{$search}%"])
                  ->orWhereRaw('address ILIKE ?', ["%{$search}%"]);

            });

        }

        // sort
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // validasi sql inject
        $allowedSorts = ['name', 'email', 'phone', 'address', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }

        // paginasi
        $perPage = $request->get('per_page', 10);

        $candidates = $query->paginate($perPage);


        return CandidateResource::collection($candidates);
    }

    public function store(Request $request)
    {

        $user = $request->user();
        $validated = $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone' => 'required|string',
            'address'   => 'required|string',
            'birth_date'   => 'date',
        ]);

        $candidateData = array_merge($validated, [
            'user_id' => $user->id,
            'email' => $user->email,

            'status' => 'applied',
        ]);

        $candidate = Candidate::create($candidateData);

        return new CandidateResource($candidate);
    }


    public function show($id)
    {
        $candidate = Candidate::findOrFail($id);

        return new CandidateResource($candidate);
    }

    public function update(Request $request, $id)
    {

        $candidateData = Candidate::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'email' => 'required|email'
        ]);

        $candidateData->update($validated);

        return new CandidateResource($candidateData);

    }


    public function destroy($id)
    {
        $candidateData = Candidate::findOrFail($id);

        $candidateData->delete();
        return response()->json([
            'message' => 'Data Pelamar berhasil dihapus!'
        ], 200);
    }
}
