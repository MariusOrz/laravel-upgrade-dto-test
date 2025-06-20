<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/test', HomeController::class)->only('index');
