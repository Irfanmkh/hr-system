<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    //

    protected $fillable = [
        'candidate_id',
        'job_id',
        'apply_date',
        'status'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobList()
    {
        return $this->belongsTo(JobList::class);
    }
}
