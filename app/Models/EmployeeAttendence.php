<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendenceList;

class EmployeeAttendence extends Model
{
    use HasFactory;

    public function attendenceMethod()
    {
        return $this->hasMany(AttendenceList::class, 'attendence_id', 'id');
    }

    public function Activity()
    {
        return $this->belongsTo(AttendenceList::class,'id' ,'attendence_id');
    }
}
