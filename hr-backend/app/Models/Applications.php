<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applications extends Model
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
        return $this->belongsTo(Candidates::class);
    }

    public function jobList()
    {
        return $this->belongsTo(JobLists::class);
    }
}
