<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use MoonShine\Laravel\Models\MoonshineUserRole;


class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'thiago@oxente.org'], 
            [
                'name' => 'Thiago',
                'surname' => 'Melo',
                'password' => Hash::make('amisome'), 
                'user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,  
                'user_plano_id' => MoonshineUserRole::DEFAULT_ROLE_ID,                        
                'ativo' => true,
            ]
        );
    }
}
