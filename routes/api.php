<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ContentModerationController;
use App\Http\Controllers\Api\PlantLogController;
use App\Http\Controllers\Api\Community\CommentController;
use App\Http\Controllers\Api\Community\PostController;

// Các route API khác sẽ do TV2-7 thêm sau.

// Nhóm API yêu cầu đăng nhập (guard web) + user không bị block
Route::middleware(['auth:web', 'active_user'])->group(function () {

    // Posts + Comments (Community)
    Route::get('/posts',           [PostController::class, 'index']);
    Route::post('/posts',          [PostController::class, 'store']);
    Route::get('/posts/{post}',    [PostController::class, 'show']);
    Route::put('/posts/{post}',    [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}',  [CommentController::class, 'destroy']);

    // Plant logs
    Route::get('/tank-plants/{tankPlant}/logs', [PlantLogController::class, 'index']);
    Route::post('/tank-plants/{tankPlant}/logs', [PlantLogController::class, 'store']);
    Route::put('/plant-logs/{plantLog}',         [PlantLogController::class, 'update']);
    Route::delete('/plant-logs/{plantLog}',      [PlantLogController::class, 'destroy']);

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
