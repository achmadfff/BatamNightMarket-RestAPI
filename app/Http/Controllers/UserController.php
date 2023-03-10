<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPackage;
use App\Transaction;
use App\Image;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function profile()
    {
        if (Auth::user()->role === 1) {
            return response()->json([
                "status" => 200,
                "message" => "success",
                "data" => [
                    "code" => Auth::user()->code,
                    "name" => Auth::user()->fullname,
                    "point" => [
                        "spend" => Auth::user()->spend(),
                        "available" => Auth::user()->point
                    ],
                    "email" => Auth::user()->email,
                    "role" => "user",
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'failed'
            ], 400);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email,'.Auth::user()->id,
            'oldPassword' => 'string',
            'newPassword' => 'string'
        ]);

        $user = new User;
        $user->fullname = $request->input('fullname');
        $user->email = $request->input('email');
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');
        $user->password = app('hash')->make($newPassword);


            if($oldPassword === '' || $newPassword === ''){
                $update = User::where('id', Auth::user()->id)->update([
                    'fullname' => $user->fullname,
                    'email' => $user->email
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'User edit successful',
                    'data' => null
                ],201);
            }else {
                if (!(Hash::check($oldPassword, Auth::user()->password))) {
                    return response()->json([
                        'status' => 406,
                        'message' => 'Your Old Password does not matches with the password you provided. Please try again.'
                    ],406);
                }else if(strcmp($oldPassword, $newPassword) == 0){
                    return response()->json([
                        'status' => 406,
                        'message' => 'New Password cannot be same as your old password. Please choose a different password.'
                    ],406);
                }else{
                    $update = User::where('id', Auth::user()->id)->update([
                        'fullname' => $user->fullname,
                        'email' => $user->email,
                        'password' => $user->password
                    ]);
                    return response()->json([
                        'status' => 201,
                        'message' => 'User edit successful',
                        'data' => null
                    ],201);
                }
            }

    }

    public function histories()
    {
        $data = [];

        $transactions = Transaction::where('user_id', Auth::user()->id)->orderBy('updated_at','DESC')->get();

        foreach ($transactions as $transaction => $t) {
            $data[] = [
                'code' => $t->package->code,
                'image' => ($t->package->image ? $t->package->image->image : null),
                'package_name' => $t->package->package_name,
                'category' => $t->package->package_category,
                'price' => [
                    'type' => 'points',
                    'value' => $t->package->package_point
                ],
                'claimed_date' => $t->created_at->format('j F Y H:i'),
                'description' => $t->package->package_description
            ];
        };

        if ($data === []) {
            return response()->json(
                [
                    'status' => 404,
                    'message' => 'You have not claim anything ',
                    'data' => null
                ],
                400
            );
        } else {
            $response = [
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ];
        }
        return response()->json($response);
    }

    public function list()
    {
        if(isset($_GET['name'])){
            $params = $_GET['name'];
            $package = UserPackage::where(function($q) use ($params){
                if(isset($params)){
                    if($params){
                        $q->where('package_name','LIKE','%'.$params.'%');
                        $q->orWhereHas('user', function($query) use ($params){
                            $query->where('fullname', 'LIKE', '%'.$params.'%');
                        });
                    }
                }
            })->paginate(10);

            $data = [];

            foreach ($package as $package => $p) {
                $data[] = [
                    'code' => $p->code,
                    'image' => ($p->image ? $p->image->image : null),
                    'name' => $p->package_name,
                    'price' => [
                        'type' => 'points',
                        'value' => $p->package_point
                    ],
                    'description' => $p->package_description,
                    'industry' => [
                        'name' => $p->user->fullname
                    ]
                ];
            };

            if($data === []){
                return response()->json([
                    'status' => 400,
                    'message' => 'Packages not found',
                    'data' => null
                ],400);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'data' => $data
                ],200);
            }
        } else {
            $data = [];

            $packages = UserPackage::paginate(10);

            foreach ($packages as $package => $p) {
            $data[] = [
                'code' => $p->code,
                'image' => ($p->image ? $p->image->image : null),
                'name' => $p->package_name,
                'price' => [
                    'type' => 'points',
                    'value' => $p->package_point
                ],
                'description' => $p->package_description,
                'industry' => [
                    'name' => $p->user->fullname
                ]
            ];
        };

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $data
            ], 200);
        }

    }
}
