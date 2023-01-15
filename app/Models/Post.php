<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'body', 'user_id'];

    public function author(){
        // 1st arg class 2nd column name powering relationship/lookup
        return $this->belongsTo(User::class, 'user_id');
    }
}
