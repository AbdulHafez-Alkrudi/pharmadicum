<?php


use App\Http\Controllers\{Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    CategoryController,
    ExpirationMedicineController,
    FavoriteMedicineController,
    MedicineController,
    OrderController,
    ReportController};
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

Route::post('register' , RegisterController::class);
Route::post('login', [LoginController::class , 'login']);

Route::middleware(['auth:api']) ->group(function(){
    Route::post('logout', LogoutController::class);
    Route::resource('order'   , OrderController::class);
    Route::resource('medicine', MedicineController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('favorite', FavoriteMedicineController::class);
    Route::resource('batch'   , ExpirationMedicineController::class );
    Route::get('report', ReportController::class);
});
