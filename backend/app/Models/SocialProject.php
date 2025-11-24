<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialProject extends Model
{
    use HasFactory;

    protected $table = 'social_projects';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'district',
        'city',
        'state',
        'zip_code',
        'phone',
        'contact_email',
        'website_url',
        'visual_color',
        'verified',
        'verified_at',
        'seal',
        'status',
        'activity_area',
        'target_audiences',
        'needs',
        'image_path',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'target_audiences' => 'array', // TEXT[] do Postgre
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define o relacionamento: um projeto pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}