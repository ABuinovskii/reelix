<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function categories()
{
    return $this->belongsToMany(Category::class);
}


}
