<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('profile_image')) {
            $file     = $request->file('profile_image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile'), $filename);
            $image_name = $filename;
        }

        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'profile_image' => $image_name ?? null,
        ];

        User::create($data);

        return response()->json([
            'message' => 'User registered successfully',
            'status'  => 201,
            'success' => true,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (! auth()->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'status'  => 422,
                'success' => false,
            ], 422);
        }

        $user = auth()->user();

        $token = $user->createToken('myToken')->accessToken;

        return response()->json([
            'user'    => $user,
            'token'   => $token,
            'message' => 'User logged in successfully',
            'status'  => 200,
            'success' => true,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user'          => auth()->user(),
            'profile_image' => asset('profile/' . auth()->user()->profile_image),
            'message'       => 'User profile retrieved successfully',
            'status'        => 200,
            'success'       => true,
        ]);
    }

    public function refreshToken(Request $request)
    {
        auth()->user()->token()->revoke();

        $user = auth()->user();

        $token = $user->createToken('myToken')->accessToken;

        return response()->json([
            'token'   => $token,
            'message' => 'Token refreshed successfully',
            'status'  => 200,
            'success' => true,
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'User logged out successfully',
            'status'  => 200,
            'success' => true
        ]);
    }
}
