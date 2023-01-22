<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use Searchable;
    use HasFactory;
    protected $fillable = ['title', 'body', 'user_id'];

    public function toSearchableArray() {
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }

    public function author(){
        // 1st arg class 2nd column name powering relationship/lookup
        return $this->belongsTo(User::class, 'user_id');
    }
}
