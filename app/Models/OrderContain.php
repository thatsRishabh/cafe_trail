<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class OrderContain extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'quantity',
        'category_id',
        'product_menu_id',
        'price',
        'netPrice',
    ];
    // working perfectly even after commenting above code

    public function  Orders()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
