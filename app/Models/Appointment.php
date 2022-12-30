<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'appointment_for',
        'appointment_with',
        'appointment_date',
        'appointment_time',
        'available_slot',
        'status',
        'is_deleted',
    ];
    function user()
    {
        return $this->hasOne(User::class, 'id', 'appointment_for');
    }
    function expert()
    {
        return $this->hasOne(User::class, 'id', 'appointment_with');
    }
    function timeSlot()
    {
        return $this->hasOne(ExpertAvailableSlot::class, 'id', 'available_slot');
    }
}
