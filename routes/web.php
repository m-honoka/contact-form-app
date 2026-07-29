<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\ContactController as UserContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. 一般ユーザー用ルート（誰でもアクセス可能）
|--------------------------------------------------------------------------
*/

// お問い合わせ入力画面（トップページ）
Route::get('/', [UserContactController::class, 'index'])->name('contact.index');

// お問い合わせ確認画面
Route::post('/contacts/confirm', [UserContactController::class, 'confirm'])->name('contact.confirm');

// お問い合わせ送信（DB保存）
Route::post('/contacts', [UserContactController::class, 'store'])->name('contact.store');

// 送信完了画面（サンクスページ）
Route::get('/thanks', function () {
    return view('contact.thanks');
})->name('contact.thanks');


/*
|--------------------------------------------------------------------------
| 2. 管理画面用ルート（要ログイン / 先頭に admin/ が付く）
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // 管理画面トップ（お問い合わせ一覧）: http://127.0.0.1/admin
    Route::get('/', [AdminContactController::class, 'index'])->name('admin.index');

    // お問い合わせ詳細画面: http://127.0.0.1/admin/contacts/1
    Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('admin.contacts.show');

    // お問い合わせ削除処理
    Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('admin.contacts.destroy');

    // 🏷️ タグ管理用ルート
    Route::post('/tags', [AdminTagController::class, 'store'])->name('admin.tags.store');
    Route::get('/tags/{tag}/edit', [AdminTagController::class, 'edit'])->name('admin.tags.edit');
    Route::put('/tags/{tag}', [AdminTagController::class, 'update'])->name('admin.tags.update');
    Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy'])->name('admin.tags.destroy');
});