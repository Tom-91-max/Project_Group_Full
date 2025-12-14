<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\PlantAdminController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ContentModerationController;

// ===== TV3 AUTH =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:web');
Route::get('/me',        [AuthController::class, 'me'])->middleware(['auth:web', 'active_user']);

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

        // ===== TV3 PLANT ADMIN =====
        Route::get('/plants',            [PlantAdminController::class, 'index']);
        Route::post('/plants',           [PlantAdminController::class, 'store']);
        Route::get('/plants/{plant}',    [PlantAdminController::class, 'show']);
        Route::put('/plants/{plant}',    [PlantAdminController::class, 'update']);
        Route::delete('/plants/{plant}', [PlantAdminController::class, 'destroy']);
    });

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
