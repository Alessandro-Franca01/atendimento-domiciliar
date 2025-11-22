<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Patient;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Get patient addresses for appointment form
Route::get('/patients/{patient}/addresses', function (Patient $patient) {
    return response()->json($patient->addresses);
});

// You can add more API routes here as needed