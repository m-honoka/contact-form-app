<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常な入力値の場合はバリデーションを通過すること
     */
    public function test_validation_passes_with_valid_data(): void
    {
        $category = Category::factory()->create();

        $validData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区1-1-1',
            'category_id' => $category->id,
            'detail' => 'これはお問い合わせのテスト本文です。',
        ];

        $response = $this->post('/contacts/confirm', $validData);

        // バリデーションエラーが起きていない（＝リダイレクト302でなく、200 OKが返る）こと
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
    }

    /**
     * 必須項目が未入力の場合にバリデーションエラーになること
     */
    public function test_validation_fails_when_required_fields_are_empty(): void
    {
        $response = $this->post('/contacts/confirm', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'last_name',
            'first_name',
            'gender',
            'email',
            'address',
            'category_id',
            'detail',
        ]);
    }

    /**
     * メールアドレスの形式が不正な場合にバリデーションエラーになること
     */
    public function test_validation_fails_with_invalid_email(): void
    {
        $category = Category::factory()->create();

        $invalidData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'invalid-email-format', // 不正なメールアドレス
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区1-1-1',
            'category_id' => $category->id,
            'detail' => 'テスト本文',
        ];

        $response = $this->post('/contacts/confirm', $invalidData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * お問い合わせ詳細（detail）が文字数制限（120文字）を超える場合にエラーになること
     */
    public function test_validation_fails_when_detail_exceeds_max_length(): void
    {
        $category = Category::factory()->create();

        $invalidData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区1-1-1',
            'category_id' => $category->id,
            'detail' => str_repeat('あ', 121), // 121文字（上限超え）
        ];

        $response = $this->post('/contacts/confirm', $invalidData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['detail']);
    }
}