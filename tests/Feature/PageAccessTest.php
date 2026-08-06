<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageAccessTest extends TestCase
{
    use RefreshDatabase; //テストごとにデータベースをリセット
    /**
     * A basic feature test example.
     */
    public function test_contact_form_page_can_be_accessed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // ビューに必要な変数が渡されているか確認
        $response->assertViewHas(['categories', 'tags']);
    }

    /**
     * サンクスページ（/thanks）の表示テスト
     */
    public function test_thanks_page_can_be_accessed(): void
    {
        $response = $this->get('/thanks');

        $response->assertStatus(200);
    }

    /**
     * 未認証ユーザーが管理画面（/admin）にアクセスした場合、ログイン画面へリダイレクトされるか
     */
    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /**
     * 認証済みユーザーが管理画面（/admin）を表示できるか
     */
    public function test_authenticated_user_can_access_admin_dashboard(): void
    {
        // ログイン用のダミーユーザーを作成
        $user = User::factory()->create();

        // ログイン状態で /admin にアクセス
        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
    }
}


