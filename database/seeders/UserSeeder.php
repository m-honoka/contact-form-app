<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder; // 💡 Userモデルをインポート
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
