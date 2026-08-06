<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // テスト用のタグを自動生成 (例: 'タグ_1', 'タグ_2' または fake()-word() など)
            'name' => $this->faker->unique()->word(),
        ];
    }
}
