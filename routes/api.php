<?php

use App\Http\Controllers\FamilyTreeController;
use App\Http\Controllers\IndonesiaController;
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

// Route::middleware('auth:api')->get('/', function (Request $request) {
//     return $request->user();
// });

// Route::get('/', function (Request $request) {
//     return;
// });

// route untuk mengambil semua anak budi
Route::get('/get-children/{id}', [FamilyTreeController::class, 'get_children']);

// route untuk mengambil semua cucu budi
Route::get('/get-grandchildren/{id}', [FamilyTreeController::class, 'get_grandchildren']);

// route untuk mengambil semua cucu perempuan budi
Route::get('/get-female-grandchildren/{id}', [FamilyTreeController::class, 'get_all_female_grandchildren']);

// route untuk mengambil bibi dari farah
Route::get('/get-all-autie/{id}', [FamilyTreeController::class, 'get_all_autie']);

// route untuk mengambil sepupu laki-laki dari hani
route::get('/get-all-male-cousing/{id}', [FamilyTreeController::class, 'get_all_male_cousing']);

// create data family
Route::post('/insert-data-parent', [FamilyTreeController::class, 'insertDataParent']);
Route::post('/insert-data-child', [FamilyTreeController::class, 'insertDataChild']);

// delete data family
Route::delete('/delete-family-tree/{id}', [FamilyTreeController::class, 'deleteFamilyTree']);

// read data family
Route::get('/get-all-family/{id}', [FamilyTreeController::class, 'getAllFamily']);

// update data family
Route::put('/update-family/{id}', [FamilyTreeController::class, 'updateFamilyTree']);


// function to get data
Route::get('/desa', [IndonesiaController::class, 'getAllDesa']);
Route::post('/desa', [IndonesiaController::class, 'createDesa']);
Route::put('/desa/{id}', [IndonesiaController::class, 'updateDesa']);
Route::get('/desa/{id}', [IndonesiaController::class, 'getDesa']);
Route::delete('/desa/{id}', [IndonesiaController::class, 'deleteDesa']);
