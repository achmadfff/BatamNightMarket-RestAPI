<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPackage;
use App\Transaction;
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

    public function histories()
    {
        $data = [];
        
        $transactions = Transaction::whereHas('package', function ($query) {
            $query->where('user_id', '=', Auth::user()->id);
        })->get();
        
        foreach($transactions as $t){
            $data[] = [
                'code' => $t->package->code,
                'image' => $t->package->image,
                'package_name' => $t->package->package_name,
                'total_item' => 1,
                'price' => [
                    'type' => 'points',
                    'value' => $t->package->package_point
                ],
                'claimed_date' => $t->created_at,
                'description' => $t->package->package_description,
                'claimed_by' => $t->package->user->fullname,
            ];
        };
        
        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => $data
        ];

        return response()->json($response);
    }
}
