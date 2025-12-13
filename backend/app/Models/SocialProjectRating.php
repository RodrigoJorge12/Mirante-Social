<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialProjectRating extends Model
{
    use HasFactory;

    protected $table = 'social_project_ratings';
    protected $fillable = [
        'social_project_id',
        'user_id',
        'rating',
        'feedback_text',
    ];
    protected $casts = [];
}
