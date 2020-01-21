<?php

namespace App\Http\Controllers;

use  App\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request)
    {   
        $images = new Image;
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
                    $path = ''. $varPath;
                    
                    $imageName = rand().'.'.$extension;
                    File::put($path.$imageName, base64_decode($image));
                    Image::create([
                        'type' => $type,
                        'image' => $varPath.$imageName
                    ]);
                    break;
                case 'place':
                    $varPath = 'images/place/';
                    $path = ''. $varPath;
                    
                    $imageName = rand().'.'.$extension;
                    File::put($path.$imageName, base64_decode($image));
                    Image::create([
                        'type' => $type,
                        'image' => $varPath.$imageName
                    ]);
                    break;
                case 'food':
                    $varPath = 'images/food/';
                    $path = ''. $varPath;
                    
                    $imageName = rand().'.'.$extension;
                    File::put($path.$imageName, base64_decode($image));
                    Image::create([
                        'type' => $type,
                        'image' => $varPath.$imageName
                    ]);
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
                    'image_url' => env('ASSETS_URL').$varPath.$imageName
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