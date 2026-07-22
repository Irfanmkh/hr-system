<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $role = strtolower($request->user()->role?->name ?? '');

        if ($role === 'candidate') {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak!'
            ], 403);
        }

        $query = Candidate::query();

        // search
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('full_name ILIKE ?', ["%{$search}%"])
                    ->orWhereRaw('email ILIKE ?', ["%{$search}%"])
                    ->orWhereRaw('phone ILIKE ?', ["%{$search}%"])
                    ->orWhereRaw('address ILIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // sort
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // whitelist kolom yg bisa di sort
        $allowedSorts = ['full_name', 'email', 'phone', 'address', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }

        // paginasi
        $perPage = $request->integer('per_page', 10);
        $candidates = $query->paginate($perPage);

        return CandidateResource::collection($candidates);
    }

    public function store(Request $request)
    {
        $role = strtolower($request->user()->role?->name ?? '');

        if (!in_array($role, ['hr admin', 'hr staff'])) {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak! '
            ], 403);
        }

        // validasi input
        $validated = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:candidates,email',
            'phone'      => 'required|string|max:20',
            'address'    => 'required|string',
            'birth_date' => 'required|date',
        ]);

        $candidate = Candidate::create($validated);
        return new CandidateResource($candidate);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $role = strtolower($user->role?->name ?? '');

        $candidate = Candidate::find($id);

        if (!$candidate) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Data kandidat tidak ditemukan.'
            ], 404);
        }

        if ($role === 'candidate' && $candidate->user_id !== $user->id) {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak! '
            ], 403);
        }

        return new CandidateResource($candidate);
    }

    public function update(Request $request, $id)
    {
        $role = strtolower($request->user()->role?->name ?? '');

        if (!in_array($role, ['hr admin', 'hr staff'])) {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak!'
            ], 403);
        }

        $candidateData = Candidate::find($id);

        if (!$candidateData) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Data kandidat tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'full_name'  => 'required|string',
            'phone'      => 'required|string',
            'address'    => 'required|string',
            'birth_date' => 'required|date',
            'email'      => 'required|email|unique:candidates,email,' . $candidateData->id,
        ]);

        $candidateData->update($validated);

        return new CandidateResource($candidateData);
    }

    public function destroy(Request $request, $id)
    {
        $role = strtolower($request->user()->role?->name ?? '');

        if (!in_array($role, ['hr admin', 'hr staff'])) {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak! '
            ], 403);
        }

        $candidateData = Candidate::find($id);

        if (!$candidateData) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Data kandidat tidak ditemukan.'
            ], 404);
        }

        $candidateData->delete();

        return response()->json([
            'success'     => true,
            'status_code' => 200,
            'message'     => 'Data Pelamar berhasil dihapus!'
        ], 200);
    }
}
