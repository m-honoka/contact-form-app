<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーが管理画面一覧にアクセスするとログイン画面へリダイレクトされること
     */
    public function test_guest_cannot_access_admin_contact_list(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /**
     * ログイン済みの管理ユーザーが一覧画面を表示できること
     */
    public function test_authenticated_user_can_view_admin_contact_list(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create([
            'last_name' => 'テスト',
            'first_name' => '太郎',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.index'); // ビュー名が異なる場合は適宜変更してください
        $response->assertSee('テスト');
        $response->assertSee('太郎');
    }

    /**
     * お問い合わせ詳細画面を表示できること
     */
    public function test_authenticated_user_can_view_contact_detail(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create([
            'detail' => '詳細ページのテスト本文です。',
        ]);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertSee('詳細ページのテスト本文です。');
    }

    /**
     * お問い合わせを削除でき、/adminへリダイレクトされること
     */
    public function test_authenticated_user_can_delete_contact(): void
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        // 管理画面トップへリダイレクトされるか
        $response->assertRedirect('/admin');

        // DBから削除されているか
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /**
     * キーワード（氏名・メールアドレス）で検索・絞り込みができること
     */
    public function test_authenticated_user_can_search_contacts_by_keyword(): void
    {
        $user = User::factory()->create();

        // 検索にヒットするデータ
        $targetContact = Contact::factory()->create([
            'last_name' => '検索対象者',
            'email' => 'search_target@example.com',
        ]);

        // 検索にヒットしないデータ
        $otherContact = Contact::factory()->create([
            'last_name' => '除外対象者',
            'email' => 'other@example.com',
        ]);

        // 氏名で検索
        $response = $this->actingAs($user)->get('/admin?keyword=検索対象者');
        $response->assertStatus(200);
        $response->assertSee('検索対象者');
        $response->assertDontSee('除外対象者');

        // メールアドレスで検索
        $response = $this->actingAs($user)->get('/admin?keyword=search_target@example.com');
        $response->assertStatus(200);
        $response->assertSee('search_target@example.com');
        $response->assertDontSee('other@example.com');
    }

    /**
     * カテゴリで絞り込みができること
     */
    public function test_authenticated_user_can_filter_contacts_by_category(): void
    {
        $user = User::factory()->create();

        $categoryA = \App\Models\Category::factory()->create(['content' => '商品について']);
        $categoryB = \App\Models\Category::factory()->create(['content' => 'その他']);

        $contactA = Contact::factory()->create([
            'category_id' => $categoryA->id,
            'last_name' => 'カテゴリAのユーザー',
        ]);
        $contactB = Contact::factory()->create([
            'category_id' => $categoryB->id,
            'last_name' => 'カテゴリBのユーザー',
        ]);

        // カテゴリAで絞り込み
        $response = $this->actingAs($user)->get("/admin?category_id={$categoryA->id}");

        $response->assertStatus(200);
        $response->assertSee('カテゴリAのユーザー');
        $response->assertDontSee('カテゴリBのユーザー');
    }

    /**
     * 性別で絞り込みができること
     */
    public function test_authenticated_user_can_filter_contacts_by_gender(): void
    {
        $user = User::factory()->create();

        $maleContact = Contact::factory()->create([
            'gender' => 1, // 男性
            'last_name' => '男性ユーザー',
        ]);
        $femaleContact = Contact::factory()->create([
            'gender' => 2, // 女性
            'last_name' => '女性ユーザー',
        ]);

        // 性別（男性 = 1）で絞り込み
        $response = $this->actingAs($user)->get('/admin?gender=1');

        $response->assertStatus(200);
        $response->assertSee('男性ユーザー');
        $response->assertDontSee('女性ユーザー');
    }

    /**
     * 日付で絞り込みができること
     */
    public function test_authenticated_user_can_filter_contacts_by_date(): void
    {
        $user = User::factory()->create();

        $todayContact = Contact::factory()->create([
            'created_at' => '2026-08-01 10:00:00',
            'last_name' => '本日分のデータ',
        ]);
        $oldContact = Contact::factory()->create([
            'created_at' => '2026-07-01 10:00:00',
            'last_name' => '過去分のデータ',
        ]);

        $response = $this->actingAs($user)->get('/admin?date=2026-08-01');

        $response->assertStatus(200);
        $response->assertSee('本日分のデータ');
        $response->assertDontSee('過去分のデータ');
    }
}