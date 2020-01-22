<?php

namespace App\Http\Controllers;

use App\User;
use Auth;

class OwnerController extends Controller
{

    public function profile()
    {

        $user = new User;
        $user->role = Auth::user()->role;

        if($user->role === 2){
            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => [
                    "name" => Auth::user()->fullname,
                    "package_claimed" => 0,
                    "balances" => [
                        "point" => 0,
                        "price" => 0
                    ],
                    "email" => Auth::user()->email,
                    "role" => "owner",
                ]
            ], 200);
        } else{
            return response()->json([
                'status' => 400,
                'message' => 'failed'
            ], 400);
        }
    }


}
