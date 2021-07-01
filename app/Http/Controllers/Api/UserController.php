<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['data' => $user], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if($user)
        {
            if(Hash::check($request->password, $user->password))
            {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json(['data' => $user, 'access-token' => $token], 200);
            } else {
                return response()->json(['data' => ''], 404);
            }
        } else {
            return response()->json(['data' => ''], 404);
        }
    }

    public function profile()
    {
        return response()->json(['data' => auth()->user()], 200);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['data' => ''], 200);
    }
}
