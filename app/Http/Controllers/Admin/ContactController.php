<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact; // 💡 お問い合わせモデルをインポート
use App\Models\Category; // 💡 カテゴリモデルをインポート
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request) // 💡 引数に Request $request を追加
    {
        // 1. セレクトボックス用のカテゴリー全件取得
        $categories = Category::all();

        // 2. 検索用のクエリビルダを開始
        $query = Contact::query();

        // 🔍 A. 名前（部分一致）の絞り込み
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('first_name', 'like', '%' . $keyword . '%');
            });
        }

        // 🔍 B. 性別の絞り込み（「0:性別」は全選択扱いとする）
        if ($request->filled('gender') && $request->input('gender') !== '0') {
            $query->where('gender', $request->input('gender'));
        }

        // 🔍 C. カテゴリの絞り込み
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 🔍 D. 日付の絞り込み
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 3. 絞り込んだ結果を7件ごとにページネーションで取得
        $contacts = $query->paginate(7);

        // 4. データをビューに渡す
        return view('admin.index', compact('categories', 'contacts'));
    }
}