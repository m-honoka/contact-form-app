<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // 💡 タグの新規追加
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:tags,name',
        ], [
            'name.required' => 'タグ名を入力してください',
            'name.unique' => 'そのタグ名は既に存在します',
        ]);

        Tag::create([
            'name' => $request->name,
        ]);

        return redirect('/admin')->with('success', 'タグを追加しました');
    }

    // 💡 タグ編集画面の表示
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    // 💡 タグの更新処理
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|max:255|unique:tags,name,' . $tag->id,
        ], [
            'name.required' => 'タグ名を入力してください',
            'name.unique' => 'そのタグ名は既に存在します',
        ]);

        $tag->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.index')->with('success', 'タグを更新しました'); // 管理画面のルーティング名に合わせて指定
    }

    // 💡 タグの削除
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect('/admin')->with('success', 'タグを削除しました');
    }

}