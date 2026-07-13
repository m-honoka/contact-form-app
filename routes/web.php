<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ContactController; // 💡 コントローラーを読み込む

// 管理画面のトップページ（お問い合わせ一覧）のルート
Route::get('/admin', [ContactController::class, 'index'])->middleware(['auth']);

// 💡 【追記】詳細画面（{contact} にデータのIDが入ります）
Route::get('/admin/contacts/{contact}', [ContactController::class, 'show'])->middleware(['auth']);

// 💡 【追記】削除処理（URLは詳細画面と同じですが、Methodが delete になります）
Route::delete('/admin/contacts/{contact}', [ContactController::class, 'destroy'])->middleware(['auth']);