<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    //SoftDeletes
    use HasApiTokens, HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
        'api_token',
        'image',
        'role',
        'gender',
        'rate',
        'address',
        'price_hourly',
        'job_category_id',
        'bio'
    ];

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

    public function generateToken()
    {
        $this->api_token = Str::random(40);
        $this->save();

        return $this->api_token;
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function userFavourites()
    {
        return $this->hasMany(UserFavourite::class);
    }

    public function favouriteUsers()
    {
        return $this->belongsToMany(User::class, 'user_favourites', 'user_id', 'craftman_id');
    }

    public function userBusniessWorks()
    {
        return $this->hasMacro(UserBusinessWork::class);
    }
}
