<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertAvailableSlot extends Model
{
    use HasFactory;

    protected $table = 'expert_available_slots';

    protected $fillable = [
        'doctor_id',
        'doctor_available_id',
        'from',
        'to',
        'is_deleted'
    ];
    function appointment(){
        return $this->hasMany(Appointment::class,'available_slot','id');
    }
}
