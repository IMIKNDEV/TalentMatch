<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/talent_app', function () {
    return view('talent_app');
})->name('talent.app');
