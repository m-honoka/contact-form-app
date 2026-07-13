<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact; // 💡 お問い合わせモデルをインポート
use App\Models\Category; // 💡 カテゴリモデルをインポート
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // 1. カテゴリーは全件取得のままでOK
        $categories = Category::all();

        // 2. ここを修正！ all() から paginate(10) に変更（1ページに10件表示）
        $contacts = Contact::paginate(10);

        // 3. データを渡して表示
        return view('admin.index', compact('categories', 'contacts'));
    }
}