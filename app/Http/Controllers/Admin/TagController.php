<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    // 💡 タグの新規追加
    public function store(TagRequest $request)
    {
        Tag::create($request->validated());

        return redirect()->route('admin.index')->with('success', 'タグを追加しました');
    }

    // 💡 タグ編集画面の表示
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    // 💡 タグの更新処理
    public function update(TagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect()->route('admin.index')->with('success', 'タグを更新しました');
    }

    // 💡 タグの削除
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.index')->with('success', 'タグを削除しました');
    }
}