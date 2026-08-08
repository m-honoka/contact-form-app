<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Tag;
class ContactController extends Controller
{
    // 🔍 検索＆一覧表示用のメソッド
    public function index(Request $request)
    {
        // 1. セレクトボックス用のカテゴリー全件取得
        $categories = Category::all();
        $tags = Tag::all();

        // モデルの scopeSearch と リレーション（with）を活用
        $contacts = Contact::search($request->all())
            ->with('category')
            ->latest() // 新しい順
            ->paginate(7);
        // 4. データをビューに渡す
        return view('admin.index', compact('categories', 'contacts', 'tags'));
    }

    // 💡 【追記】詳細表示用の show メソッド
    public function show(Contact $contact)
    {
        // adminフォルダ内の show.blade.php を表示する
        return view('admin.show', compact('contact'));
    }

    // 💡 【追記】削除処理用の destroy メソッド
    public function destroy(Contact $contact)
    {
        // 1. 該当するデータをデータベースから削除
        $contact->delete();

        // 2. 削除が終わったら、管理画面の一覧ページにリダイレクトする
        return redirect('/admin')->with('success', 'お問い合わせを削除しました。');
    }
}
