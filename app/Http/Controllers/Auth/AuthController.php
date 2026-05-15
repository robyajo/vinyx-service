<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'displayName' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|string|min:8|same:password',
        ], [
            'displayName.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'confirmPassword.same' => 'Password tidak cocok',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = User::create([
            'name' => $request->displayName,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('User');

        return $this->createdResponse($this->formatUserData($user), 'User registered successfully');
    }

    public function login(Request $request, AvatarService $avatar)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Email atau username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginType => $request->username, 'password' => $request->password])) {
            return $this->unauthorizedResponse('Email atau username salah');
        }

        $user = Auth::user();
        $avatar->ensureLocalAvatar($user);
        return $this->successResponse($this->formatUserData($user), 'Login successful');
    }

    public function me(Request $request, AvatarService $avatar)
    {
        $user = $request->user()->load('roles', 'permissions');

        $avatar->ensureLocalAvatar($user);

        return $this->successResponse(new UserResource($user), 'User profile retrieved');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $token = $user->token();
        $token->revoke();

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function refresh(Request $request, AvatarService $avatar)
    {
        $user = $request->user();

        $avatar->ensureLocalAvatar($user);

        return $this->successResponse($this->formatUserData($user), 'Token refreshed successfully');
    }

    public function socialRedirect(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function socialCallback(string $provider, AvatarService $avatar)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(config('app.frontend_url') . '/signin?error=Invalid credentials');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $username = Str::slug($socialUser->getName() ?? $socialUser->getNickname(), '');

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

        $avatar->ensureLocalAvatar($user);

        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');

        $accessToken = $user->createToken('credential-login')->accessToken;
        $refreshToken = $user->createToken('credential-refresh')->accessToken;

        return redirect($frontendUrl . '/oauth/callback?accessToken=' . $accessToken . '&refreshToken=' . $refreshToken);
    }

    private function formatUserData($user)
    {
        $accessToken = $user->createToken('credential-login')->accessToken;
        $refreshToken = $user->createToken('credential-refresh')->accessToken;

        return [
            'user' => new UserResource($user),
            'roles' => $user->roles->pluck('name')->implode(','),
            'permissions' => $user->permissions->pluck('name')->toArray(),
            'tokens' => [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
            ],
        ];
    }
}
