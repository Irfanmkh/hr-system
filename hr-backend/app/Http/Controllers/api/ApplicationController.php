<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobList;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $role = strtolower($request->user()->role?->name ?? '');

        if ($role !== 'hr admin') {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak! Anda tidak memiliki akses ke halaman ini.'
            ], 403);
        }

        $query = Application::query()->with(['candidate', 'jobList']);

        // search
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));

            $query->where(function ($q) use ($search) {
                // search name & email dari candidate table
                $q->whereHas('candidate', function ($candidateQuery) use ($search) {
                    $candidateQuery->whereRaw('name ILIKE ?', ["%{$search}%"])
                        ->orWhereRaw('email ILIKE ?', ["%{$search}%"]);
                })
                    // search title dari joblists table
                    ->orWhereHas('jobList', function ($jobQuery) use ($search) {
                        $jobQuery->whereRaw('title ILIKE ?', ["%{$search}%"]);
                    });
            });
        }

        // sort
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // whitelist kolom yg bisa di sort
        $allowedSorts = ['full_name', 'email', 'title', 'apply_date', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            match ($sortBy) {
                // Sorting via relasi Candidate
                'full_name', 'email' => $query->orderBy(
                    Candidate::select($sortBy)->whereColumn('candidates.id', 'applications.candidate_id'),
                    $sortDir
                ),
                // Sorting via relasi Job List
                'title' => $query->orderBy(
                    JobList::select('title')->whereColumn('jobs.id', 'applications.job_id'),
                    $sortDir
                ),
                // Sorting langsung dari tabel applications
                default => $query->orderBy("applications.{$sortBy}", $sortDir),
            };
        } else {
            // Default fallback
            $query->latest('applications.created_at');
        }

        // paginasi
        $perPage = $request->integer('per_page', 10);
        $applications = $query->paginate($perPage);

        return ApplicationResource::collection($applications);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $role = strtolower($user->role?->name ?? '');

        if ($role !== 'candidate') {
            return response()->json([
                'success'     => false,
                'status_code' => 403,
                'message'     => 'Akses ditolak'
            ], 403);
        }

        // Cari data candidate milik user yang login
        $candidate = Candidate::where('user_id', $user->id)->first();

        if (!$candidate) {
            return response()->json([
                'success'     => false,
                'status_code' => 404,
                'message'     => 'Profil kandidat Anda belum terdaftar.'
            ], 404);
        }

        // Validasi Job List
        $validated = $request->validate([
            'job_list_id' => 'required|exists:job_lists,id',
        ]);

        // agar tidak melamar pekerjaan yang sama
        $exists = Application::where('candidate_id', $candidate->id)
            ->where('job_list_id', $validated['job_list_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success'     => false,
                'status_code' => 409,
                'message'     => 'Anda sudah melamar pekerjaan ini.'
            ], 409);
        }

        $appData = Application::create([
            'candidate_id' => $candidate->id,
            'job_list_id'  => $validated['job_list_id'],
            'apply_date'   => now(),
            'status'       => 'applied',
        ]);

        $appData->load(['candidate', 'jobList']);

        return new ApplicationResource($appData);
    }
}
