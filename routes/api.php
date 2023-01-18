<?php

use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


date_default_timezone_set('Asia/Jerusalem');

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

/**Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::resource('job-categories', JobCategoryController::class)->except(['create', 'edit']);
Route::delete('job-categories/delete/{id}', [JobCategoryController::class, 'destroy']);
Route::post('job-categories/update/{id}', [JobCategoryController::class, 'update']);

Route::group(['middleware' => 'CustomersApi'], function() {
    Route::resource('jobs', JobController::class)->except(['create', 'edit']);
    Route::delete('jobs/delete/{id}', [JobController::class, 'destroy']);
    Route::post('jobs/update/{id}', [JobController::class, 'update']);

    Route::post('proposal/add_proposal', [JobController::class, 'addProposal']);
    Route::post('proposal/accept_proposal/{id}', [JobController::class, 'acceptProposal']);
    Route::put('proposal/finish_proposal/{id}', [JobController::class, 'finishProposal']);
    Route::put('proposal/rate_proposal/{id}', [JobController::class, 'rateProposal']);

    Route::put('user/update_profile', [UserController::class, 'updateProfile']);
    Route::get('user/notifications', [UserController::class, 'notifications']);
    Route::put('user/read_notification/{id}', [UserController::class, 'readNotification']);
    Route::post('user/logout', [UserController::class, 'logout']);

    Route::post('user/add_business_work/{id}', [UserController::class, 'addBusinessWork']);
    Route::get('user/{id}', [UserController::class, 'view']);
    Route::get('user/business_works/{id}', [UserController::class, 'businessWorks']);
    Route::get('user/favourite_craftmans/{id}', [UserController::class, 'favouriteCraftmans']);
    Route::post('user/add_favourite_craftman/{id}/{craftman_id}', [UserController::class, 'addFavouriteCraftman']);
    Route::delete('user/remove_favourite_craftman/{id}/{craftman_id}', [UserController::class, 'removeFavouriteCraftman']);


});
Route::post('user/registeration', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);



