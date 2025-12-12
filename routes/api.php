<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ContentModerationController;

// TV2 controllers
use App\Http\Controllers\Api\TankApiController;
use App\Http\Controllers\Api\TankPlantController;

// Các route API khác sẽ do TV2-7 thêm sau.

// Nhóm API yêu cầu đăng nhập (guard web) + user không bị block
Route::middleware(['auth:web', 'active_user'])->group(function () {

    // =======================
    // ADMIN API
    // =======================
    Route::prefix('admin')->middleware('admin')->group(function () {
        // User management
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole']);
        Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);

        // Content moderation
        Route::delete('/questions/{question}', [ContentModerationController::class, 'deleteQuestion']);
        Route::delete('/posts/{post}', [ContentModerationController::class, 'deletePost']);
        Route::delete('/comments/{comment}', [ContentModerationController::class, 'deleteComment']);
    });

    // =======================
    // TV2 - TANKS API
    // =======================
    Route::get('/tanks',           [TankApiController::class, 'index']);
    Route::post('/tanks',          [TankApiController::class, 'store']);
    Route::get('/tanks/{tank}',    [TankApiController::class, 'show']);
    Route::put('/tanks/{tank}',    [TankApiController::class, 'update']);
    Route::delete('/tanks/{tank}', [TankApiController::class, 'destroy']);

    // =======================
    // TV2 - TANK PLANTS API
    // =======================
    Route::post('/tanks/{tank}/plants',         [TankPlantController::class, 'store']);
    Route::delete('/tank-plants/{tankPlant}',   [TankPlantController::class, 'destroy']);
});

// Route test: xem user hiện tại (chỉ cần login + active_user)
Route::middleware(['auth:web', 'active_user'])->get('/me', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'success' => true,
        'data'    => [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => $user->role,
            'status' => $user->status,
        ],
        'message' => 'Current authenticated user',
    ]);
});

// Route test: chỉ Admin mới vào được
Route::middleware(['auth:web', 'active_user', 'admin'])->get('/admin/ping', function (Request $request) {
    return response()->json([
        'success' => true,
        'data'    => [
            'message' => 'Admin area OK',
            'user'    => [
                'id'   => $request->user()->id,
                'name' => $request->user()->name,
            ],
        ],
        'message' => 'You are admin and active.',
    ]);
});
