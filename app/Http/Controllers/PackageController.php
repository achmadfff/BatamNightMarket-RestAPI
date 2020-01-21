<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'package_name' => 'required|string',
            'package_point' => 'required|integer',
            'package_category' => 'required|string',
            'package_description' => 'required|string',
        ]);

        try {
            $package = new UserPackage;
            $package->package_name = $request->input('package_name');
            $package->package_point = $request->input('package_point');
            $package->package_category = $request->input('package_category');
            $package->package_description = $request->input('package_description');
            function generateRandomString($length = 20) {
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }
            $package->code = generateRandomString(5);
            $package->user_id = Auth::user()->id;
            $package->save();
            

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
        $package = new UserPackage;
        $get_package = UserPackage::where('code', $code)->first();

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => [
                'name' => $get_package->package_name,
                'point' => 0,
                'category' => $get_package->package_category,
                'description' => $get_package->package_description,
                'image' => 'dummy'
            ]
            ], 200);
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
                'image' => 'dummy',
                'package_name' => $detail->package_name,
                'price' => [
                    'type' => 'points',
                    'value' => 0
                ],
            ],
                'description' => $detail->package_description
            ], 200);
    }

    public function claim()
    {
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => null
        ], 200);
    }
}
