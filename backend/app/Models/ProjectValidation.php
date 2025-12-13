<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectValidation extends Model
{
    use HasFactory;

    protected $table = 'project_validations';
    protected $fillable = [
        'social_project_id',
        'channel',
        'destination',
        'code',
        'status',
        'expires_at',
        'verified_at',
        'attempts',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
