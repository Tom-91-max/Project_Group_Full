<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ContentModerationController;

// Các route API khác sẽ do TV2-7 thêm sau.
use App\Http\Controllers\Api\WaterLogApiController;

Route::middleware(['auth:web', 'active_user'])->group(function () {
    // Lấy danh sách water logs của một tank
    Route::get('/tanks/{tank}/water-logs', [WaterLogApiController::class, 'index']);
    
    // Tạo water log mới cho tank
    Route::post('/tanks/{tank}/water-logs', [WaterLogApiController::class, 'store']);
    
    // Bonus: Chi tiết một log cụ thể (nếu cần)
    Route::get('/water-logs/{waterLog}', [WaterLogApiController::class, 'show']);
    
    // Bonus: Xóa log (nếu cần)
    Route::delete('/water-logs/{waterLog}', [WaterLogApiController::class, 'destroy']);
});

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
