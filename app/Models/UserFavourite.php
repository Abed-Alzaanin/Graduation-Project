<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavourite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'craftman_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function craftmanUser()
    {
        return $this->belongsTo(User::class, 'craftman_id');
    }
}
