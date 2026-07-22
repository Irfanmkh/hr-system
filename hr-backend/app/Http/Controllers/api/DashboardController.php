<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\JobList;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function index(Request $request)
    {
        $role = $request->user()->role?->name;

        if ($role === 'candidate') {
            return response()->json([
                'message' => 'Akses ditolak!.'
            ], 403);
        }
        $totalCandidates = Candidate::count();
        $totalJobs = JobList::count();

        $candidatesByStatus = Candidate::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get();
        return response()->json([
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'total_candidates' => $totalCandidates,
                'total_jobs' => $totalJobs,
                'candidates_by_status' => $candidatesByStatus,
            ]
        ], 200);
    }
}
