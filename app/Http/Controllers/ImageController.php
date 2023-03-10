<?php

namespace App\Http\Controllers;

use  App\Image;
use Auth;
use App\UserPackage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request)
    {   
        $image = $request->image;  // your base64 encoded
        $type = $request->type;

        if (!$image && !$type) {
            $data = [
                'status' => 400,
                'message' => 'Please fullfil required parameters',
                'data' => null
            ];
            return response()->json($data, 200);
        }

        $extensions = ['png', 'jpeg', 'jpg'];
        $allowed = false;
        $extension = null;
        foreach ($extensions as $ext) {
            if (preg_match('/data:image\/'.$ext.';/', $image)) {
                $allowed = true;
                $extension = $ext;
            }
        }
        
        if ($allowed) {
            $image = str_replace('data:image/'.$extension.';base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            switch ($type) {
                case 'event':
                    $varPath = 'images/event/';
                    $imageName = rand().'-'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                    File::put($varPath.$imageName, base64_decode($image));
                    break;
                case 'place':
                    $varPath = 'images/place/';
                    $imageName = rand().'-'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                    File::put($varPath.$imageName, base64_decode($image));
                    break;
                case 'food':
                    $varPath = 'images/food/';
                    $imageName = rand().'-'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                    File::put($varPath.$imageName, base64_decode($image));
                    break;
                default:
                    $data = [
                        'status' => 400,
                        'message' => 'Image Type not found',
                        'data' => null
                    ];
                    return response()->json($data, 200);
                    break;
            }

            $data = [
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'image_url' => env('APP_url').'/'.$varPath.$imageName
                ]
            ];
        }else{
            $data = [
                'status' => 400,
                'message' => 'Extension not allowed',
                'data' => null
            ];
        }
        return response()->json($data, 200);
    }
}