<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // updateの場合は現在のIDを除外するユニーク制約を設定
        $tagId = $this->route('tag') ? $this->route('tag')->id : null;

        return [
            'name' => 'required|max:255|unique:tags,name,' . $tagId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'タグ名を入力してください',
            'name.unique' => 'そのタグ名は既に存在します',
        ];
    }
}