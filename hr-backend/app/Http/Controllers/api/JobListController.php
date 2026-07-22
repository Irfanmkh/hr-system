<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListResource;
use Illuminate\Http\Request;
use App\Models\JobList;

class JobListController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = JobList::query();

        // search

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(department) LIKE ?', ["%{$search}%"]);

            });

        }

        // filter status (is_active)
        if ($request->has('is_active') && $request->input('is_active') !== null && $request->input('is_active') !== '') {
            $isActive = $request->boolean('is_active');
            $query->where('is_active', $isActive);
        }

        // sort
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // whitelist kolom yg bisa di sort (mencegah sql injection)
        $allowedSorts = ['title', 'department', 'is_active', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }

        // paginasi
        $perPage = $request->integer('per_page', 10);
        $jobs = $query->paginate($perPage);


        return JobListResource::collection($jobs);
    }

    public function store(Request $request)
    {
        $role = $request->user()->role?->name;
        if (!in_array($role, ['hr admin', 'hr staff'])) {
            return response()->json([
                'message' => 'Akses ditolak.'
            ], 403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $job = JobList::create($validated);

        return new JobListResource($job);
    }



    public function update(Request $request, $id)
    {
        $role = $request->user()->role?->name;
        if (!in_array($role, ['hr admin', 'hr staff'])) {
            return response()->json([
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $joblist = JobList::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'department' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $joblist->update($validated);

        return new JobListResource($joblist);

    }


    public function destroy(Request $request, $id)
    {
        $role = $request->user()->role?->name;
        if (!in_array($role, ['hr admin', 'hr staff'])) {
            return response()->json([
                'message' => 'Akses ditolak.'
            ], 403);
        }
        $joblist = JobList::findOrFail($id);

        $joblist->delete();
        return response()->json([
            'message' => 'lowongan kerja berhasil dihapus!'
        ], 200);
    }
}
