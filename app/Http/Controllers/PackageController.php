<?php

namespace App\Http\Controllers;

use  App\UserPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public  function generateRandomString($length = 5) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
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
            $package->code = $this->generateRandomString(5) ;
            $package->save();
            

            return response()->json([
                'status' => 201,
                'message' => 'success',
                'data' => $package
            ], 201);
        
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Package Registration Failed!'], 409);
        }
        
    }

    public function edit()
    {
        $package = new UserPackage;
        $get_package = UserPackage::get('package_name');

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => [
                'name' => $get_package
            ]
            ], 200);


        
        
    }

    // public function edit($code, Request $request)
    // {
    //     $user = new UserPackage ;
    //     $user->code = findOrfail($code);

    //     $this->validate($request , [
    //         'package_name' => 'required|string',
    //         'package_point' => 'required|integer',
    //         'package_category' => 'required|string',
    //         'package_description' => 'required|string',
    //     ]);

    //     $user->package_name = $request->input('package_name');
    //     $user->package_point = $request->input('package_point');
    //     $user->package_category = $request->input('package_category');
    //     $user->package_description = $request->input('package_description');

    //     $update = $user->save();

    //     if($update)
    //     {
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'success',
    //             'data' => [
    //                 'name' => $user->package_name,
    //                 'point' => Auth::user()->package_point,
    //                 'category' => Auth::user()->package_category,
    //                 'description' => Auth::user()->package_description,
    //                 'image' => dummy
    //             ]
    //         ]);
    //     }
    // }
}
