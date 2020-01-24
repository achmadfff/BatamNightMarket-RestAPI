<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPackage;
use App\Transaction;
use App\Image;
use Auth;
class UserController extends Controller
{

    public function profile()
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
                        "available" => Auth::user()->point
                    ],
                    "email" => Auth::user()->email,
                    "role" => "user",
                ]
            ], 200);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'failed'
            ], 400);
        }
    }

    public function histories()
    {
        $data = [];
        $transactions = Transaction::where('user_id', Auth::user()->id)->get();
       

        foreach($transactions as $transaction => $t){
            $data[] = [
                'code' => $t->package->code,
                'image' => ($t->package->image->count() > 0 ? $t->package->image[0]->image : null),
                'package_name' => $t->package->package_name,
                'total_item' => 1,
                'price' => [
                    'type' => 'points',
                    'value' => $t->package->package_point
                ],
                'claimed_date' => $t->created_at,
                'description' => $t->package->package_description
            ];
        };
        
        if($data === []){
        $response = [
            'status' => 404,
            'message' => 'Anda belum Claim apa-apa'
        ];
        }else{
            $response = [
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ];
        }
        return response()->json($response);
    }

}
