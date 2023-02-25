<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
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

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
    }

    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
