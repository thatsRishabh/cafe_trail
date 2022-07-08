<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RecipeContains;

class Recipe extends Model
{
    use HasFactory;

    public function recipeMethods()
    {
        return $this->hasMany(RecipeContains::class, 'recipe_id', 'id');
    }
}
