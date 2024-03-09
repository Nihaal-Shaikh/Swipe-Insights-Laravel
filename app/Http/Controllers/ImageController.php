<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageStatus;
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

    public function updateImageStatus(Request $request)
    {
        // Get the first 55 keys from the request
        $first5Keys = array_slice($request->keys(), 0, 5);

        // Get all unique status values from the first 55 keys
        $statusValues = array_unique($request->only($first5Keys));

        // Get the IDs of the statuses dynamically
        $statusIds = ImageStatus::whereIn('status', $statusValues)->pluck('id', 'status');

        // Loop through the keys of the request
        foreach ($first5Keys as $key) {
            // Extract the image name (e.g., "image_33.jpg")
            $imageName = basename($key);

            // Find the last occurrence of underscore
            $lastUnderscorePos = strrpos($imageName, '_');
        
            if ($lastUnderscorePos !== false) {
                // Replace the last underscore with a dot
                $imageName = substr_replace($imageName, '.', $lastUnderscorePos, 1);
            }
            // Query the images table and update status and updated_by
            $image = Image::where('image_name', $imageName)->first();
            
            if ($image) {
                // Update status and updated_by
                $statusValue = $request->get($key);
                $statusId = $statusIds[$statusValue] ?? null;
                // dd($statusId);
    
                if ($statusId !== null) {
                    $image->image_status_id = $statusId;
                    $image->updated_by = $request->get('user_id');
                    $image->save();
                }
            }
        }
        dd($statusIds);
    }
}
