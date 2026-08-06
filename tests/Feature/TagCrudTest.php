<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagCrudTest extends TestCase
{
    use RefreshDatabase;

    /* -------------------------------------------------------------------------- */
    /* 1. 認証済みユーザーのテスト（CRUD操作）
    /* -------------------------------------------------------------------------- */

    /**
     * ログインユーザーはタグ編集画面（GET /admin/tags/{tag}/edit）を表示できる
     */
    public function test_authenticated_user_can_access_tag_edit_page(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit");

        $response->assertStatus(200);
    }

    /**
     * ログインユーザーはタグを作成でき、/admin にリダイレクトされる（POST /admin/tags）
     */
    public function test_authenticated_user_can_create_tag(): void
    {
        $user = User::factory()->create();
        $tagData = ['name' => '新規テストタグ'];

        $response = $this->actingAs($user)->post('/admin/tags', $tagData);

        // /admin へリダイレクトされたか
        $response->assertRedirect('/admin');
        // DBに保存されたか
        $this->assertDatabaseHas('tags', $tagData);
    }

    /**
     * ログインユーザーはタグを更新でき、/admin にリダイレクトされる（PUT /admin/tags/{tag}）
     */
    public function test_authenticated_user_can_update_tag(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '旧タグ名']);

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", [
            'name' => '更新後のタグ名',
        ]);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '更新後のタグ名',
        ]);
    }

    /**
     * ログインユーザーはタグを削除でき、/admin にリダイレクトされる（DELETE /admin/tags/{tag}）
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

    /* -------------------------------------------------------------------------- */
    /* 2. 未認証（ゲスト）ユーザーのアクセス制限テスト
    /* -------------------------------------------------------------------------- */

    /**
     * 未認証ユーザーのタグ操作は拒否され、/login にリダイレクトされる
     */
    public function test_guest_cannot_access_tag_management(): void
    {
        $tag = Tag::factory()->create();

        // 編集画面アクセス拒否
        $this->get("/admin/tags/{$tag->id}/edit")->assertRedirect('/login');

        // 新規作成拒否
        $this->post('/admin/tags', ['name' => 'ゲストタグ'])->assertRedirect('/login');

        // 更新拒否
        $this->put("/admin/tags/{$tag->id}", ['name' => '変更'])->assertRedirect('/login');

        // 削除拒否
        $this->delete("/admin/tags/{$tag->id}")->assertRedirect('/login');
    }
}
/**
 * A basic feature test example.
 */


