<?php

use Illuminate\Http\Request;

// フォロー
Route::put('/user/{id}', 'UserController@follow')->name('user.follow');
// アンフォロー
Route::delete('/user/{id}', 'UserController@unfollow');

// 会員登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
// ログインユーザー
Route::get('/user', fn() => Auth::user())->name('user');

// 写真投稿
Route::post('/photos', 'PhotoController@create')->name('photo.create');
// 写真一覧 ページネーション
Route::get('/photos', 'PhotoController@index')->name('photo.index');
// 写真一覧取得 全て
Route::get('/photo', 'PhotoController@all')->name('photo.all');
// ログインユーザーの写真一覧
Route::get('/own', 'PhotoController@own')->name('photo.own');
// 写真削除
Route::delete('/photos/{id}/delete', 'PhotoController@delete')->name('photo.delete');
// 写真詳細
Route::get('/photos/{id}', 'PhotoController@show')->name('photo.show');
// コメント
Route::post('/photos/{photo}/comments', 'PhotoController@addComment')->name('photo.comment');
// いいね
Route::put('/photos/{id}/like', 'PhotoController@like')->name('photo.like');
// いいね解除
Route::delete('/photos/{id}/like', 'PhotoController@unlike');
// トークンリフレッシュ
Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return response()->json();
});
