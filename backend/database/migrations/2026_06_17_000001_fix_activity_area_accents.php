<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $fixes = [
            'Educacao'          => 'Educação',
            'Saude'             => 'Saúde',
            'Esporte'           => 'Esporte',
            'Assistencia Social'=> 'Assistência Social',
            'Meio Ambiente'     => 'Meio Ambiente',
            'Cultura'           => 'Cultura',
        ];

        foreach ($fixes as $wrong => $correct) {
            DB::table('social_projects')
                ->where('activity_area', $wrong)
                ->update(['activity_area' => $correct]);
        }
    }

    public function down(): void {}
};
