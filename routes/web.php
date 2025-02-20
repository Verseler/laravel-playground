<?php

use App\Http\Controllers\ProfileController;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $shops = Shop::all();
    $products = Product::all();

    return Inertia::render('Dashboard', ['shops' => $shops, 'products' => $products]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/create', function (Request $request) {
    $shop = Shop::find(1);
    $shop->products()->attach(1);

    return back();
})->name('product.shop');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
