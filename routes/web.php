<?php

use App\Filament\Pages\OrderPage;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', OrderPage::class)->name('order-page');