<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Candidate;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    //
    public function index(Request $request)
    {
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

        // validasi sql inject

        $allowedSorts = ['name', 'email', 'title', 'apply_date', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            // Sorting Nama atau Email Candidate
            if (in_array($sortBy, ['name', 'email'])) {
                $query->join('candidates', 'applications.candidate_id', '=', 'candidates.id')
                      ->orderBy("candidates.{$sortBy}", $sortDir)
                      ->select('applications.*');
            }
            //Sorting Judul Lowongan
            elseif ($sortBy === 'title') {
                $query->join('job_lists', 'applications.job_list_id', '=', 'job_lists.id')
                      ->orderBy('job_lists.title', $sortDir)
                      ->select('applications.*');
            }
            // Sorting berdasarkan Kolom Tabel Applications
            else {
                $query->orderBy("applications.{$sortBy}", $sortDir);
            }
        } else {
            // fallback jika sort_by tidak dikirim
            $query->latest('applications.created_at');
        }


        // paginasi
        $perPage = $request->get('per_page', 10);

        $applications = $query->paginate($perPage);


        return ApplicationResource::collection($applications);
    }

    public function store(Request $request)
    {

        $user = $request->user();

        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();


        $validated = $request->validate([
                'job_list_id' => 'required|exists:job_lists,id',
            ]);

        $exists = Application::where('candidate_id', $candidate->id)
                ->where('job_list_id', $validated['job_list_id'])
                ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Anda sudah melamar pekerjaan ini.'
            ], 409);
        }



        $appData = Application::create([
            'candidate_id' => $candidate->id,
            'job_list_id' => $validated['job_list_id'],
            'apply_date' => now(),
            'status' => 'applied',
        ]);

        $appData->load(['candidate', 'jobList']);

        return new ApplicationResource($appData);
    }

}
