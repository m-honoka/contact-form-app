<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use App\Models\Category;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // 💡 性別をランダムに決定 (1:男性, 2:女性, 3:その他 など仕様書の定義に合わせてください)
        // 今回は仕様書に基づき、1〜3の数値をランダムに生成します
        $gender = $this->faker->numberBetween(1, 3);

        return [
            // 既存のカテゴリからランダムに1つのIDを取得して紐付ける
            'category_id' => Category::inRandomOrder()->first()->id,

            // 日本語の姓名
            'first_name' => $this->faker->lastName(),  // 姓
            'last_name' => $this->faker->firstName($gender == 1 ? 'male' : 'female'),   // 名

            'gender' => $gender,
            'email' => $this->faker->safeEmail(),

            // 電話番号（ハイフンなし、最大11桁の数字文字列。マイグレーションの string('tel', 11) に合わせる）
            'tel' => substr($this->faker->phoneNumber(), 0, 11),

            // 住所と建物名（マイグレーションの building に合わせる）
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(), // 「〇〇ビル102」のようなリアルな値

            // お問い合わせ詳細（120文字以内）
            'detail' => $this->faker->realText(100),
        ];
    }
}
