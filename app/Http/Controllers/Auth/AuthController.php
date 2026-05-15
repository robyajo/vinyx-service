<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'username.required' => 'Username wajib diisi',
            'username.max' => 'Username maksimal 255 karakter',
            'username.unique' => 'Username sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Password tidak cocok',
            'password_confirmation.required' => 'Konfirmasi Password wajib diisi',
            'password_confirmation.min' => 'Konfirmasi Password minimal 8 karakter',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = User::create([

            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('User');

        return $this->createdResponse($this->formatUserData($user), 'User registered successfully');
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginType => $request->login, 'password' => $request->password])) {
            return $this->unauthorizedResponse('Email atau username salah');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $this->successResponse($this->formatUserData($user), 'Login successful');
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles', 'permissions');

        return $this->successResponse([
            'user' => $user,
            'roles' => $user->roles->pluck('name')->implode(','),
            'permissions' => $user->permissions->pluck('name')->toArray(),
        ], 'User profile retrieved');
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        /** @var \Laravel\Passport\Token $token */
        $token = $user->token();
        $token->revoke();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Refresh the user's access token.
     */
    public function refresh(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->successResponse($this->formatUserData($user), 'Token refreshed successfully');
    }

    /**
     * Redirect to social provider.
     */
    public function socialRedirect(string $provider)
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        return $driver->stateless()->redirect();
    }

    /**
     * Handle social provider callback.
     */
    public function socialCallback(string $provider)
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver($provider);

            $socialUser = $driver->stateless()->user();
        } catch (\Exception $e) {
            return redirect(config('app.frontend_url') . '/login?error=Invalid credentials');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $username = Str::slug($socialUser->getName() ?? $socialUser->getNickname(), '');

            // Ensure unique username
            $originalUsername = $username;
            $count = 1;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $count++;
            }

            $user = User::create([

                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'username' => $username,
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
                'avatar' => $socialUser->getAvatar(),
            ]);

            $user->assignRole('User');
        } else {
            $user->update([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        $userData = $this->formatUserData($user);

        // Redirect back to frontend with token
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        return redirect($frontendUrl . '/auth/callback?token=' . $userData['accessToken']);
    }

    private function formatUserData($user)
    {
        return [
            'user' => $user,
            'roles' => $user->roles->pluck('name')->implode(','),
            'permissions' => $user->permissions->pluck('name')->toArray(),
            'accessToken' => $user->createToken('credential-login')->accessToken,
        ];
    }
}
