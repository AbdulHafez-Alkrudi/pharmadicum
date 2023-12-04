<?php

use App\Http\Controllers\{Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    CategoryController,
    ExpirationMedicineController,
    FavoriteMedicineController,
    MedicineController,
    OrderController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';
