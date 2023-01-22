<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];
    // an accessor
    protected function avatar(): Attribute {
        return Attribute::make(get: function($value){
            return $value ? '/storage/avatars/' . $value : '/fallback-avatar.jpg';
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function feedPosts(){
        // 1st arg model end up with, 2nd intermediate data to be looked up 3rd foreign key on intermediate table, 4th = foreign key on final model 5th = local key and 6th = local key on intermediate table
        return $this->hasManyThrough(Post::class, Follow::class, 'user_id', 'user_id', 'id', 'followeduser');
    }

    public function posts(){
        // user has many posts relationship
        return $this->hasMany(Post::class, 'user_id');
    }

    public function followers(){
        // can add 3rd argument for local key but in most cases it will be id and is therefore not needed
      return $this->hasMany(Follow::class, 'followeduser');
    }

    public function followingUsers(){
        return $this->hasMany(Follow::class, 'user_id');
    }
}
