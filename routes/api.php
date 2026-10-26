<?php

use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

Route::apiResource('peminjaman', PeminjamanController::class);
