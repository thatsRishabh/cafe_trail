<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    public function roleid()
    {
        return $this->belongsTo(User::class,'user_id' ,'id');
    }
}

