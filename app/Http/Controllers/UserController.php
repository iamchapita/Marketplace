<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:80',
            'lastName' => 'required|string|max:80',
            'dni' => 'required|string|min:15',
            'email' => 'required|email|unique:users|max:255',
            'phoneNumber' => 'required|string',
            'birthDate' => 'required|date',
            'password' => 'required|min:10',
            'isAdmin' => 'required|boolean',
            'isClient' => 'required|boolean',
            'isSeller' => 'required|boolean'
        ]);

        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        // Check if validation pass then create user and auth token. Return the auth token
        if ($validator->passes()) {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = User::create([
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'dni' => $request->dni,
                    'email' => $request->email,
                    'phoneNumber' => $request->phoneNumber,
                    'birthDate' => $request->birthDate,
                    'isAdmin' => $request->isAdmin,
                    'isClient' => $request->isClient,
                    'isSeller' => $request->isSeller,
                    'password' => Hash::make($request->password)
                ]);
                // $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'user' => $user
                ], 200);
            }
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $authuser = auth()->user();
            return response()->json(['message' => 'Login successful'], 200);
        } else {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged Out'], 200);
    }
}
