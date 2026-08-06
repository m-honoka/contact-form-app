<?php

namespace App\Http\Controllers; // 💡 Adminが付かない、通常の階層です

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // 💡 ユーザー側のお問い合わせ入力画面を表示する
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        // ユーザー側の入力画面（resources/views/contact/index.blade.phpなど）を指定
        return view('contact.index', compact('categories', 'tags'));
    }

    // 💡 【追記】確認画面を表示するメソッド
    public function confirm(StoreContactRequest $request)
    {
        // 1. 入力されたすべてのデータを取得
        $validated = $request->all();


        $tel = $request->input('tel1') . $request->input('tel2') . $request->input('tel3');
        $validated['tel'] = $tel;

        // フォームで選択された category_id をもとにカテゴリー情報を1件取得
        $category = Category::find($request->input('category_id'));

        // 💡 2. フォームでチェックされた tags (配列) があれば、該当する Tag モデルのコレクションを取得
        $tags = Tag::whereIn('id', $request->input('tags', []))->get();

        // 3. 確認画面のBlade（contact/confirm.blade.phpなど）を表示し、データを渡す
        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function store(StoreContactRequest $request)
    {
        // 1. 送信されたデータから、DBに保存する項目を取得
        $data = $request->only([
            'category_id',
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'building',
            'detail',
        ]);

        // 2. データベースのお問い合わせテーブル（contacts）に保存
        $contact = Contact::create($data);

        // 3. もしタグ（中間テーブル）がある場合は同期保存
        if ($request->has('tags')) {
            $contact->tags()->sync($request->input('tags'));
        }

        // 4. 保存完了後、サンクスページにリダイレクト（または表示）
        return view('contact.thanks'); // または redirect()->route('contact.thanks');
    }
}