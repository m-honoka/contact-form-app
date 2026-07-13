<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 💡 依存関係を考慮して、正しい順番でシーダーを一括実行します
        $this->call([
            UserSeeder::class,     // 1. 管理者ユーザー登録
            CategorySeeder::class, // 2. カテゴリマスター（Contactより先！）
            TagSeeder::class,      // 3. タグマスター（Contactより先！）
            ContactSeeder::class,  // 4. お問い合わせと中間テーブルのダミーデータ生成
        ]);
    }
}