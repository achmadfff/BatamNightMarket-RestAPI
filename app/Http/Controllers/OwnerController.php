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
        $claimed = Transaction::where('status', 'claimed')->whereHas('package', function ($query) {
            $query->where('user_id', '=', Auth::user()->id);
        })->has('user')->count();

        if (Auth::user()->role === 2) {
            $pending_transaction = Transaction::where([['status', '=', 'pending']])->whereHas('package', function($q){
                $q->where('user_id', Auth::user()->id);
            })->orderBy('id', 'DESC')->first();

            if ($pending_transaction) {
                $popup = [
                    "show" => true,
                    "type" => "claim",
                    "title" => "You got new order!",
                    "description" => $pending_transaction->user->fullname." claiming your ".$pending_transaction->package->package_name."'s package (".$pending_transaction->package->package_point." points)",
                    "data" => [
                        "transaction_id" => $pending_transaction->id
                    ]
                ];
            }else{
                $popup = [
                    "show" => false,
                    "type" => null,
                    "title" => null,
                    "description" => null
                ];
            }

            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => [
                    "code" => Auth::user()->code,
                    "name" => Auth::user()->fullname,
                    "package_claimed" => $claimed,
                    "balances" => [
                        "point" => Auth::user()->point,
                        "price" => 0
                    ],
                    "email" => Auth::user()->email,
                    "role" => "owner",
                    "popup" => $popup
                ]
            ], 200);
        } else {
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
        })->has('user')->get();
        foreach ($transactions as $t) {
            $data[] = [
                'code' => $t->package->code,
                'image' => ($t->package->image->count() > 0 ? $t->package->image->image : null),
                'package_name' => $t->package->package_name,
                'package_category' => $t->package->package_category,
                'price' => [
                    'type' => 'points',
                    'value' => $t->package->package_point
                ],
                'claimed_date' => $t->created_at,
                'description' => $t->package->package_description,
                'claimed_by' => $t->user->fullname,
            ];
        };

        $response = [
            'status' => 200,
            'message' => 'success',
            'data' => $data
        ];

        return response()->json($response);
    }

    public function list()
    {   
        $data = [];
        $packages = UserPackage::where('user_id', Auth::user()->id)->get();

        foreach($packages as $package){
            $data[] = [
                'code' => $package->code,
                'name' => $package->package_name,
                'image' => ($package->image->count() > 0  ? $package->image->image : null),
                'price' => [
                    'type' => 'points',
                    'value' => $package->package_point
                ],
                'description' => $package->package_description
            ];
        };

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $data
        ],200);
    }
}
