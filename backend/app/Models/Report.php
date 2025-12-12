<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'social_project_id',
        'reporter_user_id',
        'category',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'resolution_notes',
    ];
}
