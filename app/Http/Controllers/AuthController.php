<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use function Pest\Laravel\json;

class AuthController extends Controller
{
    use ApiResponses;

    public function profile(Request $request)
    {
        return new UserResource($request->user());
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        $message = 'User logged in  successfully.';

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user->createToken($request->input('device_name'))->plainTextToken,
        ], $message);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        $message = 'User registered successfully.';

        return $this->success([
            'user' => new UserResource($user),
            'token' => $user->createToken($request->input('device_name'))->plainTextToken,
        ], $message);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'You have successfully been logged out and your token has been deleted.',
        ]);
    }
}
