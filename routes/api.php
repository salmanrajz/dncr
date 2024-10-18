<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NumberUploadController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::post('/login', function (Request $request) {
//     $credentials = $request->only('email', 'password');

//     if (Auth::attempt($credentials)) {
//         return response()->json(['success' => true]);
//     } else {
//         return response()->json(['success' => false, 'message' => 'Invalid credentials']);
//     }
// });
Route::post('login', [AdminController::class, 'login'])->name('login.vue');
Route::post('/upload', [AdminController::class, 'upload']);
Route::post('/upload-numbers', [NumberUploadController::class, 'uploadNumbers']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get-customers/{customerId}', [AdminController::class, 'getAllData']);
    Route::post('/submit-feedback', [AdminController::class, 'storeFeedback']);
    Route::get('/numberdetails', [AdminController::class, 'numberdetails']);
    Route::get('/dncr_number', [AdminController::class, 'dncr_number']);
    Route::post('/check-master-data', [AdminController::class, 'checkMasterData']);
    Route::get('/dashboard-stats/{userId}', [AdminController::class, 'getDashboardStats']);
    Route::post('/upload-number-file', [AdminController::class, 'uploadNumberFile']);
    Route::get('/available-numbers', [AdminController::class, 'AvailableNumber']);
    Route::post('/UploadMasterData', [AdminController::class, 'UploadNumber']);
    Route::post('/UploadMasterDataDNCR', [AdminController::class, 'UploadMasterDataDNCR']);
    Route::get('/get-user-data', [UserController::class, 'getUserData']);
    Route::get('/user-dashboard', [UserController::class, 'userDashboard']);
    Route::post('/upload-dncr', [NumberUploadController::class, 'UploadDNCR']);

});
