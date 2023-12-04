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
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register' , [RegisterController::class , 'register']);
Route::post('login', [LoginController::class , 'login']);



Route::middleware(['auth:api']) ->group(function(){
    Route::post('logout', [LogoutController::class, 'logout']);
    Route::resource('order'   , OrderController::class);
    Route::resource('medicine', MedicineController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('favorite', FavoriteMedicineController::class);
    Route::resource('batch'   , ExpirationMedicineController::class );
});
