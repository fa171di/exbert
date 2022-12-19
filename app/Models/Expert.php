<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;

    protected $table = 'experts';

    protected $fillable = [
        'user_id',
        'title',
        'fees',
        'degree',
        'experience',
        'slot_time',
        'is_deleted',
    ];
}
