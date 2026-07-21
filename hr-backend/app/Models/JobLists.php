<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobLists extends Model
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
