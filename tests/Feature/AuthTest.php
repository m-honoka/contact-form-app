<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しいログイン情報でログインでき、管理画面（/admin）へリダイレクトされること
     */
    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // ログイン状態になっていること
        $this->assertAuthenticatedAs($user);

        // 管理画面へリダイレクトされること
        $response->assertRedirect('/admin');
    }

    /**
     * 誤ったパスワードの場合はログインに失敗し、未認証のままであること
     */
    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // ゲスト状態（未ログイン）のままであること
        $this->assertGuest();

        // セッションにエラーが含まれていること
        $response->assertSessionHasErrors();
    }

    /**
     * ログアウト処理が正常に行われ、ログイン画面へリダイレクトされること
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        // ログイン状態からログアウトAPI/ルートを実行
        $response = $this->actingAs($user)->post('/logout');

        // 未ログイン状態になっていること
        $this->assertGuest();

        // ログイン画面へリダイレクトされること（仕様により / や /login へ変更）
        $response->assertRedirect('/');
    }
}