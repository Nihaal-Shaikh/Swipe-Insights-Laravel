<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function getImages()
    {
        // Fetch 5 active images from the database
        $images = Image::where('image_status_id', '!=', 0)
                       ->whereIn('image_status_id', [1, 2])
                       ->take(5)
                       ->pluck('image_name');
    
        // Append the base URL using asset() function
        $baseUrl = asset('storage/images/');
    
        // Append the base URL to each image name
        $imageUrls = $images->map(function ($imageName) use ($baseUrl) {
            return $baseUrl . '/' . $imageName;
        });
    
        return response()->json(['images' => $imageUrls]);
    }
}
