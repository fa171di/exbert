<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertAvailableDay extends Model
{
    use HasFactory;

    protected $table ='expert_available_days';
    protected $fillable = [
        'expert_id',
        'mon',
        'sun',
        'tue',
        'wen',
        'thu',
        'fri',
        'sat'
    ];
}
