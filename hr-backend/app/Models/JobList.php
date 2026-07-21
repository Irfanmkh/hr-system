<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobList extends Model
{
    //

    protected $fillable = [
        'id',
        'title',
        'department',
        'description',
        'is_active'
    ];


}
