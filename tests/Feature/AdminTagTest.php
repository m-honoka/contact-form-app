<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーがタグ一覧を表示できること
     */
    public function test_authenticated_user_can_view_tag_list(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => 'テストタグ']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('テストタグ');
    }

    /**
     * 新しいタグを作成できること
     */
    public function test_authenticated_user_can_create_tag(): void
    {
        $user = User::factory()->create();

        $tagData = [
            'name' => '新規タグ名',
        ];

        $response = $this->actingAs($user)->post('/admin/tags', $tagData);

        // 作成後にタグ一覧へリダイレクトされるか（ルートの設定に合わせて変更してください）
        $response->assertRedirect('/admin');

        // データベースに保存されているか
        $this->assertDatabaseHas('tags', [
            'name' => '新規タグ名',
        ]);
    }

    /**
     * タグを更新できること
     */
    public function test_authenticated_user_can_update_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '更新前タグ']);

        $updateData = [
            'name' => '更新後タグ',
        ];

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", $updateData);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '更新後タグ',
        ]);
    }

    /**
     * タグを削除できること
     */
    public function test_authenticated_user_can_delete_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}