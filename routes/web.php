<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEB ROUTES – UI DEMO
|--------------------------------------------------------------------------
*/

// HOME
Route::view('/', 'home')->name('home');

// TANKS
Route::prefix('tanks')->group(function () {
    Route::view('/my_tanks', 'tanks.my_tanks')->name('tanks.my_tanks');
    Route::view('/tank_detail', 'tanks.tank_detail')->name('tanks.tank_detail');
    Route::view('/add_plant_to_tank', 'tanks.add_plant_to_tank')->name('tanks.add_plant_to_tank');
    Route::view('/tank_form', 'tanks.tank_form')->name('tanks.tank_form');
});

// Q&A UI
Route::prefix('qa')->group(function () {
    Route::view('/questions_list', 'qa.questions_list')->name('qa.questions_list');
    Route::view('/my_questions', 'qa.my_questions')->name('qa.my_questions');
    Route::view('/ask_question', 'qa.ask_question')->name('qa.ask_question');
    Route::view('/question_detail', 'qa.question_detail')->name('qa.question_detail');
});

// COMMUNITY
Route::prefix('community')->group(function () {
    Route::view('/posts_list', 'community.posts_list')->name('community.posts_list');
    Route::view('/post_detail', 'community.post_detail')->name('community.post_detail');
    Route::view('/my_posts', 'community.my_posts')->name('community.my_posts');
    Route::view('/create_post', 'community.create_post')->name('community.create_post');
});

// PLANTLOG
Route::prefix('plantlog')->group(function () {
    Route::view('/plant_logs_list', 'plantlog.plant_logs_list')
        ->name('plantlog.plant_logs_list');

    Route::view('/plant_log_form', 'plantlog.plant_log_form')
        ->name('plantlog.plant_log_form');
});

// MONITORING
Route::prefix('monitoring')->group(function () {
    Route::view('/water_logs', 'monitoring.water_logs')
        ->name('monitoring.water_logs');
});

// IMAGE IDENTIFY (FIX LỖI)
Route::prefix('image')->group(function () {
    Route::view('/identify_plant', 'image.identify_plant')
        ->name('image.identify_plant');
});

// AUTH UI (USER)
Route::view('/login', 'auth.login_user')->name('auth.login_user');
Route::view('/register', 'auth.register_user')->name('auth.register_user');
Route::view('/forgot-password', 'auth.forgot_password')->name('auth.forgot_password');

// ADMIN UI
Route::prefix('admin')->group(function () {
    Route::view('/login', 'auth.login_admin')->name('admin.login');
    Route::view('/dashboard', 'admin.dashboard_admin')->name('admin.dashboard');

    Route::view('/users', 'admin.users_list')->name('admin.users.index');
    Route::view('/users/create', 'admin.user_form')->name('admin.users.create');

    Route::view('/qa', 'admin.qa_moderation')->name('admin.qa_moderation');
    Route::view('/community-posts', 'admin.posts_moderation')->name('admin.posts_moderation');

    Route::view('/plants', 'admin.plants_list')->name('admin.plants_list');
    Route::view('/plants/create', 'admin.plant_form')->name('admin.plant_form');
});
