<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Recipe;

class RecipeContains extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'quantity',
        'unit_id'
    ];
    // working perfectly even after commenting above code

    public function recipes()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }
    
}
