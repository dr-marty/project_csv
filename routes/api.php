<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CSVController;

Route::post('/upload-csv', [CSVController::class, 'upload']);
Route::get('/export-csv', [CSVController::class, 'export']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function () {
    return response()->json(['message' => 'welcome to  from Laravel API']);
});
