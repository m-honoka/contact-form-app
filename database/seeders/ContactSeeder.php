<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Tag;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 先ほど作ったデータをランダム生成するFactoryを使って、20件のContactを作成する
        Contact::factory()->count(20)->create()->each(function ($contact) {

            // 既存のタグから、ランダムに1〜3件のタグをランダムな順番で取得する
            $randomTagIds = Tag::inRandomOrder()
                ->take(rand(1, 3)) // 1〜3件をランダムに決定
                ->pluck('id');     // IDだけの配列（例: [1, 4]）にする

            // 💡 多対多のリレーションメソッド tags() を使って中間テーブルに紐付ける
            $contact->tags()->attach($randomTagIds);
        });
    }
}