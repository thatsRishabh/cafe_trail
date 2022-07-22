<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function subCategory()
    {
         return $this->hasMany(self::class,'parent_id','id');
    }

    // public function recipeMethods()
    // {
    //     return $this->hasMany(RecipeContains::class, 'recipe_id', 'id');
    // }
}
