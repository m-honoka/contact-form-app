<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーション通過時に確認ページが表示され、入力内容が含まれていること
     */
    public function test_contact_confirm_page_shows_input_data(): void
    {
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);

        $formData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区1-1-1',
            'building_name' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせの内容テストです。',
        ];

        $response = $this->post('/contacts/confirm', $formData);

        // 正常レスポンス確認
        $response->assertStatus(200);
        // ビューが正しいか
        $response->assertViewIs('contact.confirm'); // または実際のビュー名
        // 入力した内容が画面に表示されているか
        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('test@example.com');
        $response->assertSee('テストカテゴリ');
    }

    /**
     * 確認ページアクセス時、バリデーションエラーの場合はリダイレクトされること
     */
    public function test_contact_confirm_redirects_on_validation_error(): void
    {
        // 必須項目を空にして送信
        $response = $this->post('/contacts/confirm', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /**
     * お問い合わせ送信時にDBにデータが保存され、タグが記録され、/thanksにリダイレクトされること
     */
    public function test_contact_can_be_submitted_and_saved_to_database(): void
    {
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $submitData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '0901234567',
            'address' => '東京都渋谷区1-1-1',
            'category_id' => $category->id,
            'detail' => '送信完了テストの本文です。',
            'tags' => $tags->pluck('id')->toArray(), // タグIDの配列
        ];

        $response = $this->post('/contacts', $submitData);

        // サンクスビューが正常表示されたか（実装に合わせて200を検証）
        $response->assertStatus(200);
        $response->assertViewIs('contact.thanks');

        // contacts テーブルにレコードが保存されたか
        $this->assertDatabaseHas('contacts', [
            'last_name' => '山田',
            'first_name' => '太郎',
            'email' => 'test@example.com',
        ]);

        // 中間テーブル (contact_tag) にタグが紐付いているか
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('contact_tag', [
                'tag_id' => $tag->id,
            ]);
        }
    }

    /**
     * お問い合わせ送信時、バリデーションエラーの場合はリダイレクトされること
     */
    public function test_contact_submit_redirects_on_validation_error(): void
    {
        $response = $this->post('/contacts', []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
