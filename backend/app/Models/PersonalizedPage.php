<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalizedPage extends Model
{
    use HasFactory;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'social_project_id',
        'caption',
        'url',
        'template'
    ];

    /**
     * Define o relacionamento: uma pagina pertence a um projeto social
     */
    public function socialProject()
    {
        return $this->belongsTo(SocialProject::class, "social_project_id");
    }
}