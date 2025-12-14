<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AuthController extends BaseApiController
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data = Arr::except($data, ['password_confirmation']);
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        auth()->login($user);

        return $this->success($user, 'Registered & logged in.');
    }

    public function login(LoginRequest $request)
    {
        if (!auth()->attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials.', 401);
        }

        $user = auth()->user();

        if (($user->status ?? 'active') !== 'active') {
            auth()->logout();
            return $this->error('Account is blocked.', 403);
        }

        return $this->success($user, 'Logged in.');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->success(null, 'Logged out.');
    }

    public function me(Request $request)
    {
        return $this->success($request->user());
    }
}
