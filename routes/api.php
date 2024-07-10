<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json([
        'status' => false,
        'message' => 'akses tidak diperbolehkan',
    ]);
})->name('login');

Route::get('/product', [ProductController::class, 'index'])->name('product.index')->middleware('auth:sanctum', 'ability:product-list');
Route::post('/product', [ProductController::class, 'store'])->name('product.store')->middleware('auth:sanctum', 'ability:product-store');
Route::post('/registeruser', [AuthUserController::class, 'registerUser'])->name('auth.registerUser');
Route::post('/loginuser', [AuthUserController::class, 'loginUser'])->name('auth.loginUser');
Route::post('/filluser', [AuthUserController::class, 'filluserrole']);
