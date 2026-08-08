<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * お問い合わせ一覧API
     * GET /api/v1/contacts でJSON形式の一覧が返り、検索・ページネーションが機能し、バリデーションエラー時は422が返ること。
     */
    public function test_can_get_contact_list_with_pagination_and_search(): void
    {
        $category = Category::factory()->create();
        Contact::factory()->create(['first_name' => '太郎', 'category_id' => $category->id]);
        Contact::factory()->create(['first_name' => '次郎', 'category_id' => $category->id]);

        // 一覧取得と検索のテスト (200 OK)
        $response = $this->getJson('/api/v1/contacts?keyword=太郎');

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => '太郎'])
            ->assertJsonMissing(['first_name' => '次郎']);
    }

    /**
     * お問い合わせ詳細API
     * GET /api/v1/contacts/{id} でJSON形式の詳細が返り、存在しないIDで404エラーJSONが返ること。
     */
    public function test_can_get_contact_detail_and_returns_404_if_not_found(): void
    {
        $contact = Contact::factory()->create();

        // 成功パターン (200 OK)
        $response = $this->getJson("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $contact->id]);

        // 存在しないIDパターン (404 Not Found)
        $notFoundResponse = $this->getJson('/api/v1/contacts/99999');
        $notFoundResponse->assertStatus(404);
    }

    /**
     * お問い合わせ作成API
     * POST /api/v1/contacts でレコードが作成され201が返り、バリデーションエラー時は422が返ること。
     */
    public function test_can_create_contact_and_validates_input(): void
    {
        $category = Category::factory()->create();

        $validData = [
            'category_id' => $category->id,
            'first_name' => 'テスト',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせ内容テスト',
        ];

        // 成功パターン (201 Created)
        $response = $this->postJson('/api/v1/contacts', $validData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('contacts', ['email' => 'test@example.com']);

        // バリデーションエラーパターン (422 Unprocessable Content)
        $invalidResponse = $this->postJson('/api/v1/contacts', []);
        $invalidResponse->assertStatus(422);
    }

    /**
     * お問い合わせ更新API
     * PUT /api/v1/contacts/{id} でレコードが更新され200が返り、存在しないIDで404、バリデーションエラー時は422が返ること。
     */
    public function test_can_update_contact_and_handles_errors(): void
    {
        $contact = Contact::factory()->create();

        $updateData = [
            'category_id' => $contact->category_id,
            'first_name' => '更新太郎',
            'last_name' => '更新山田',
            'gender' => 1,
            'email' => 'update@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => '更新後の詳細テキストです',
        ];

        // 成功パターン (200 OK)
        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $updateData);
        // エラー内容を出力して確認する
        //$response->dump();

        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', ['first_name' => '更新太郎']);

        // 存在しないIDパターン (404 Not Found)
        $notFoundResponse = $this->putJson('/api/v1/contacts/99999', $updateData);
        $notFoundResponse->assertStatus(404);

        // バリデーションエラーパターン (422 Unprocessable Content)
        $invalidResponse = $this->putJson("/api/v1/contacts/{$contact->id}", ['email' => 'invalid-email']);
        $invalidResponse->assertStatus(422);
    }

    /**
     * お問い合わせ削除API
     * DELETE /api/v1/contacts/{id} でレコードが削除され204が返り、存在しないIDで404が返ること。
     */
    public function test_can_delete_contact_and_returns_404_if_not_found(): void
    {
        $contact = Contact::factory()->create();

        // 成功パターン (204 No Content)
        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);

        // 存在しないIDパターン (404 Not Found)
        $notFoundResponse = $this->deleteJson('/api/v1/contacts/99999');
        $notFoundResponse->assertStatus(404);
    }
}