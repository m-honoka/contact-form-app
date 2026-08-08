<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    // 🔍 検索用ローカルスコープ（ここを追加）
    public function scopeSearch(Builder $query, array $filters): Builder
    {
        // A. 名前（姓・名）部分一致
        $query->when($filters['keyword'] ?? null, function ($q, $keyword) {
            $q->where(function ($subQuery) use ($keyword) {
                $subQuery->where('last_name', 'like', "%{$keyword}%")
                    ->orWhere('first_name', 'like', "%{$keyword}%");
            });
        });

        // B. 性別（'0' 以外が指定されている場合）
        $query->when(($filters['gender'] ?? null) && $filters['gender'] !== '0', function ($q) use ($filters) {
            $q->where('gender', $filters['gender']);
        });

        // C. カテゴリ
        $query->when($filters['category_id'] ?? null, function ($q, $categoryId) {
            $q->where('category_id', $categoryId);
        });

        // D. 日付
        $query->when($filters['date'] ?? null, function ($q, $date) {
            $q->whereDate('created_at', $date);
        });

        return $query;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
