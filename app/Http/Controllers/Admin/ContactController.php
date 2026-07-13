<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // 🔍 検索＆一覧表示用のメソッド
    public function index(Request $request)
    {
        // 1. セレクトボックス用のカテゴリー全件取得
        $categories = Category::all();

        // 2. 検索用のクエリビルダを開始
        $query = Contact::query();

        // A. 名前（部分一致）の絞り込み
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('first_name', 'like', '%' . $keyword . '%');
            });
        }

        // B. 性別の絞り込み
        if ($request->filled('gender') && $request->input('gender') !== '0') {
            $query->where('gender', $request->input('gender'));
        }

        // C. カテゴリの絞り込み
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // D. 日付の絞り込み
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 3. 7件ごとにページネーションで取得
        $contacts = $query->paginate(7);

        // 4. データをビューに渡す
        return view('admin.index', compact('categories', 'contacts'));
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