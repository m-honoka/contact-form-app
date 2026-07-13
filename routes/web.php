<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ContactController; // 💡 コントローラーを読み込む

// 管理画面のトップページ（お問い合わせ一覧）のルート
Route::get('/admin', [ContactController::class, 'index'])->middleware(['auth']);