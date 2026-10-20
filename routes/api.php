<?php


// use App\Http\Controllers\EVerifyController;
// use App\Http\Middleware\AllowedIps;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use App\Models\User;

// // WinForms logs in here to get a token
// Route::post('/login', function (Request $r) {
//     $r->validate(['email' => 'required|email', 'password' => 'required']);

//     $user = User::where('email', $r->email)->first();

//     if (!$user || !Hash::check($r->password, $user->password)) {
//         return response()->json(['message' => 'Invalid credentials.'], 401);
//     }

//     return ['token' => $user->createToken('winforms')->plainTextToken];
// })->middleware([AllowedIps::class, 'throttle:5,1']);

// // Protected eVerify routes
// Route::middleware(['auth:sanctum', AllowedIps::class, 'throttle:30,1'])->group(function () {
//     Route::post('/everify/verify', [EVerifyController::class, 'verify']);
//     Route::get('/everify/liveness-config', fn () => response()->json([
//         'public_api_key' => config('everify.public_api_key'),
//     ]));
// });

use App\Http\Controllers\EVerifyController;
use App\Http\Middleware\AllowedIps;
use App\Http\Middleware\ApiKeyAuth;
use Illuminate\Support\Facades\Route;

Route::middleware([ApiKeyAuth::class, AllowedIps::class, 'throttle:30,1'])
    ->group(function () {
        Route::post('/everify/verify', [EVerifyController::class, 'verify']);
        Route::get('/everify/liveness-config', fn () => response()->json([
            'public_api_key' => config('everify.public_api_key'),
        ]));
    });