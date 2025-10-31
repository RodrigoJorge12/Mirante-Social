<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco
     */
    protected $table = 'validations';

    /**
     * Chave primária (por padrão o Laravel já usa 'id')
     */
    protected $primaryKey = 'id';

    /**
     * Indica que a tabela não tem updated_at automático
     */
    public $timestamps = true;

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'type',
        'user_id',
        'code',
        'time',
    ];

    /**
     * Define o relacionamento: uma validação pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * (Opcional) Formata automaticamente o campo time se for timestamp no futuro
     * No seu caso, 'time' é VARCHAR(20), mas caso queira mudar para datetime no banco,
     * você pode usar esse accessor:
     */
    // protected $casts = [
    //     'time' => 'datetime',
    //     'created_at' => 'datetime',
    // ];
}