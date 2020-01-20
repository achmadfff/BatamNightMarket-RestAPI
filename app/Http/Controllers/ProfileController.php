<?php

namespace App\Http\Controllers;

use App\User;
use Auth;

class ProfileController extends Controller
{

    public function index()
    {

        $user = new User;
        $user->role = Auth::user()->role;
        if($user->role === 1){
            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => [
                    "name" => Auth::user()->fullname,
                    "point" => [
                        "spend" => 0,
                        "available" => 0
                    ],
                    "email" => Auth::user()->email,
                    "role" => "user",
                ]
            ], 200);

        }
        else if($user->role === 2){
            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => [
                    "name" => Auth::user()->fullname,
                    "points" => [
                        "spend" => 0,
                        "available" => 0
                    ],
                    "email" => Auth::user()->email,
                    "role" => "owner",
                ]
            ], 200);

        }


        
    }


}
