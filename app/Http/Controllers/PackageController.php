<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPackage;
use App\Transaction;
use App\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'point' => 'required|integer',
            'category' => 'required|string',
            'description' => 'required|string',
            'image' => 'string',
        ]);

        try {
            function generateRandomString($length = 20)
            {
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }
            $package = new UserPackage;
            $package->package_name = $request->input('name');
            $package->package_point = $request->input('point');
            $package->package_category = $request->input('category');
            $package->package_description = $request->input('description');
            $package->code = generateRandomString(5);
            $package->user_id = Auth::user()->id;
            $package->status = 'active';
            $images = new Image;
            $images->image = $request->input('image');
            if(Auth::user()->role === 2){
                if ($images->image === '') {
                    UserPackage::create([
                        'package_name' => $package->package_name,
                        'package_point' => $package->package_point,
                        'package_category' => $package->package_category,
                        'package_description' => $package->package_description,
                        'code' => $package->code,
                        'user_id' => $package->user_id,
                        'status' => 'active'
                    ]);
                } else {
                    
                    $package = new UserPackage;
                    $package->package_name = $request->input('name');
                    $package->package_point = $request->input('point');
                    $package->package_category = $request->input('category');
                    $package->package_description = $request->input('description');
                    $package->code = generateRandomString(5);
                    $package->user_id = Auth::user()->id;
                    $package->status = 'active';
                    $package->save();
                    $images = new Image;
                    $images->image = $request->input('image');

                    Image::create([
                        'type' => $package->package_category,
                        'image' => $images->image,
                        'package_id' => $package->id
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Failed',
                    'data' => null
                ], 400);

            }
            return response()->json([
                'status' => 201,
                'message' => 'success',
                'data' => null
            ], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Package Registration Failed!'], 409);
        }
    }

    public function edit()
    {
        $code = $_GET['code'];
        $get_package = UserPackage::where('code', $code)->first();
        if($get_package->user_id === Auth::user()->id){
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'name' => $get_package->package_name,
                    'point' => $get_package->package_point,
                    'category' => $get_package->package_category,
                    'description' => $get_package->package_description,
                    'image' => ($get_package->image->count() > 0 ? $get_package->image->image : null)
                    ]
                ], 200);
        }else{
        return response()->json([
            'status' => 400,
            'message' => 'failed'
        ], 400);
    }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'point' => 'required|integer',
            'category' => 'required|string',
            'description' => 'required|string',
            'image' => 'string'
        ]);

        $package = new UserPackage;
        $package->package_name = $request->input('package_name');
        $package->package_point = $request->input('package_point');
        $package->package_category = $request->input('package_category');
        $package->package_description = $request->input('package_description');
        $package->code = $request->code;
        $image = new Image;
        $image->image = $request->input('package_image');
        $varPath = 'image/'.$package->package_category.'/';
        if($image->image === ''){
            UserPackage::where('code', $request->code)->update([
                'package_name' => $package->package_name,
                'package_point' => $package->package_point,
                'package_category' => $package->package_category,
                'package_description' => $package->package_description
            ]);
        }else {
            UserPackage::where('code', $request->code)->update([
                'package_name' => $package->package_name,
                'package_point' => $package->package_point,
                'package_category' => $package->package_category,
                'package_description' => $package->package_description
            ]);
            Image::where('package_id', $request->id )->update([
                'type' => $package->package_category,
                'image' => env('APP_url').'/'.$varPath.$image->image
            ]);
        }
    }

    public function detail()
    {
        $code = $_GET['code'];
        $detail_package = new UserPackage;
        $detail = UserPackage::where('code', $code)->first();
        
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'code' => $code,
                    'image' => ($detail->image->count() > 0 ? $detail->image->image : null),
                    'package_name' => $detail->package_name,
                    'price' => [
                        'type' => 'points',
                        'value' => $detail->package_point
                    ],
                ],
                'description' => $detail->package_description
            ], 200);
    }

    public function claim(Request $request)
    {

        $user = User::where('code', $request->owner)->first();
        $package = UserPackage::where([['code', '=', $request->code], ['user_id', '=', $user->id]])->first();

        if ($package) {
            if (Auth::user()->point >= $package->package_point) {
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'package_id' => $package->id,
                    'status' => 'pending'
                ]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'success',
                    'data' => null
                ], 200);
            } else {
                // Response point kurang
                return response()->json([
                    'status' => 400,
                    'message' => 'Your points are not enough'
                ], 400);
            }
        } else {
            // Response gagal
            return response()->json([
                'status' => 400,
                'message' => 'The code or owner that you insert wrong'
            ], 400);
        }
    }

    public function response_claim(Request $request)
    {
        if (Auth::user()->role == 2) {
            $transaction = Transaction::where([['status','=','pending'],['id','=', $request->transaction_id]])->whereHas('package', function($q){
                $q->where('user_id', Auth::user()->id);
            })->first();
    
            if ($transaction) {
                if ($request->response == 'accept') {
                    $transaction->status = 'claimed';
                    $transaction->save();
                    
                    $owner = User::where('id', Auth::user()->id)->first();
                    $owner->point = ($owner->point + $transaction->package->package_point);
                    $owner->save();

                    $buyer = User::where('id', $transaction->user_id)->first();
                    $buyer->point = ($buyer->point - $transaction->package->package_point);
                    $buyer->save();

                    $response = [
                        'status' => 200,
                        'message' => "Claim Accepted",
                        'data' => null
                    ];
                }else{
                    $transaction->status = 'rejected';
                    $transaction->save();

                    $response = [
                        'status' => 200,
                        'message' => "Claim Rejected",
                        'data' => null
                    ];
                }
            }else {
            }
        }else{
            $response = [
                'status' => 200,
                'message' => 'Account unauthorized to access this request',
                'data' => null
            ];
        }
        return response()->json($response, $response['status']);
    }

    public function recomendation()
    {
        $detail_package = UserPackage::limit(5)->get();
        $data = [
            'status' => 200,
            'message' => 'success',
            'data' => []
        ];
        foreach ($detail_package as $detail => $d) {
            $data['data'][$detail] = [
                'code' => $d->code,
                'name' => $d->package_name,
                'image' => ($d->image->count() > 0  ? $d->image->image : null),
                'price' => [
                    'type' => 'points',
                    'value' => $d->package_point
                ],
                'description' => $d->package_description
            ];
        }
        return response()->json($data, 200);
    }
}
