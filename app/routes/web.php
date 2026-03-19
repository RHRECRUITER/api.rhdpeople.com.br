<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ATS DP
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

Route::get('/landingpage', function () {
    return view('landing.index');
});
