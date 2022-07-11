<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderContain;

class Order extends Model
{
    use HasFactory;

    public function orderContains()
    {
        return $this->hasMany(OrderContain::class, 'order_id', 'id');
    }
}
