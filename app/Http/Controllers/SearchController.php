<?php

namespace App\Http\Controllers;

use App\UserPackage;
use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function search(Request $request)
    {
        $params = $_GET['name'];
        $package = UserPackage::where('package_name','LIKE','%'.$params.'%')->orWhereHas('user', function($query){
            $params = $_GET['name'];
            $query->where('fullname', 'LIKE', '%'.$params.'%');
        })->paginate(10);

        $data = ($package->count()>0 ? $package : null);
        if($data === null){
            return response()->json([
                'status' => 400,
                'message' => 'Packages not found',
                'page' => $package->currentPage(),
                'data' => $data
            ],400);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'page' => $package->currentPage(),
                'data' => $data
            ],200);
        }
    }
}
