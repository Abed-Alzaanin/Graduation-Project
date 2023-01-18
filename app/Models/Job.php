<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'price', 'period', 'address1', 'address2', 'city', 'job_category_id','user_id', 'status'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function jobProposals()
    {
        return $this->hasMany(JobProposal::class);
    }

    public function jobImages()
    {
        return $this->hasMany(JobImage::class);
    }
}
