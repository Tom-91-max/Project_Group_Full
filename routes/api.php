<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\QA\QuestionController;
use App\Http\Controllers\Api\QA\AnswerController;

/*
|--------------------------------------------------------------------------
| API LOGIN (TEST – SESSION AUTH)
|--------------------------------------------------------------------------
*/

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email'
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (! $user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    Auth::login($user);

    return response()->json([
        'success' => true,
        'message' => 'Logged in',
        'data' => [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => $user->role,
            'status' => $user->status,
        ]
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED API
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web', 'active_user'])->group(function () {

    // Current user
    Route::get('/me', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'status' => $user->status,
            ],
            'message' => 'Current authenticated user',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Q&A API – TV5
    |--------------------------------------------------------------------------
    */

    // Questions
    Route::get('/questions',        [QuestionController::class, 'index']);
    Route::post('/questions',       [QuestionController::class, 'store']);
    Route::get('/questions/{question}', [QuestionController::class, 'show']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    // Answers
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store']);
    Route::put('/answers/{answer}',   [AnswerController::class, 'update']);
    Route::delete('/answers/{answer}',[AnswerController::class, 'destroy']);
    Route::post('/answers/{answer}/accept', [AnswerController::class, 'accept']);
});
