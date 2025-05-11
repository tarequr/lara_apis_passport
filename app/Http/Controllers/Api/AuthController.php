<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $image_name = null;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile'), $filename);
            $image_name = $filename;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'banner_image' => $image_name
        ];

        User::create($data);

        return response()->json([
            'message' => 'User registered successfully',
            'status'  => 201,
            'success' => true
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
            'success' => true
        ]);
    }

    public function profile(Request $request)
    {
        //
    }

    public function refreshToken(Request $request)
    {
        //
    }

    public function logout(Request $request)
    {
        //
    }
}
