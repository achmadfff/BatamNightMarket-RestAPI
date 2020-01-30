<?php

namespace App\Http\Controllers;

use  App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        try {

            $user = new User;
            $user->code = strtoupper(strtotime(date("Ymd")).Str::random(10));
            $user->fullname = $request->input('fullname');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->role = 1;
            $user->point = 0;
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            $credentials = $request->only(['email', 'password']);

            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if (Auth::user()->role === 1) {
                return $this->respondWithTokenuser($token);
            } else if (Auth::user()->role === 2) {
                return $this->respondWithTokenowner($token);
            } else {
                return response()->json(['message' => 'User Registration Failed!'], 409);
            }
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    public function login(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'The email or password you entered is incorrect'], 401);
        }
        if (Auth::user()->role === 1) {
            return $this->respondWithTokenuser($token);
        } else if (Auth::user()->role === 2) {
            return $this->respondWithTokenowner($token);
        } else {
            return response()->json(['message' => 'User Login Failed!'], 409);
        }
    }
}
