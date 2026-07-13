<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // 💡 Userモデルをインポート
use Illuminate\Support\Facades\Hash; // 💡 パスワードをハッシュ化するクラスをインポート

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // 💡 ここでハッシュ化（暗号化）しています
        ]);
    }
}
