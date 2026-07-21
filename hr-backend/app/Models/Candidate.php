<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    //

    protected $fillable = [
        'id',
        'full_name',
        'user_id',
        'email',
        'phone',
        'address',
        'birth_date',
        'status'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
