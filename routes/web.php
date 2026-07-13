<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ログイン後にリダイレクトされる管理画面トップの仮ルート
Route::get('/admin', function () {
    return '管理画面トップページ（ログイン成功！）';
})->middleware(['auth']); // 💡 ログインしていないと入れないようにガードをかける
