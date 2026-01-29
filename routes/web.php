<?php

use App\Http\Controllers\Management\PermissionManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/management/set-permissions', [PermissionManagementController::class, 'showPermissionPage']);
