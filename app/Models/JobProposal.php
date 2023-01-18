<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProposal extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'user_id', 'type', 'price', 'period', 'description', 'status', 'accepted_at','rate', 'rate_message'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
