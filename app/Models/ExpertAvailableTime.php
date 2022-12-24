<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertAvailableTime extends Model
{
    use HasFactory;
    protected $table = 'expert_available_times';
    protected $fillable = [
        'expert_id',
        'from',
        'to',
        'is_deleted'
    ];
}
