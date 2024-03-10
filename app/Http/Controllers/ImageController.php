<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageStatus;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function getImages()
    {
        // Fetch 5 active images from the database
        $images = Image::where('active', 1)
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
        try {
            $swipeData = $request->input('swipeData');
            $userId = intval($request->input('user_id'));

            // Extract unique values from swipeData
            $uniqueValues = array_unique($swipeData);

            // Get the IDs of the statuses dynamically
            $statusIds = ImageStatus::whereIn('status', $uniqueValues)->pluck('id', 'status')->toArray();

            // Loop through the keys of the request
            foreach ($swipeData as $imageUrl => $status) {
                // Extract the image name (e.g., "image_33.jpg")
                $imageName = basename($imageUrl);
            
                // Query the images table and update status and updated_by
                $image = Image::where('image_name', $imageName)->first();
            
                if ($image) {
                    // Update status and updated_by
                    $statusId = $statusIds[$status] ?? null;
            
                    if ($statusId !== null) {
                        $image->image_status_id = $statusId;
                        $image->customer_id = $userId;
                        $image->save();
                    }
                }
            }            
            return response()->json(['success' => true, 'message' => 'Image status updated successfully']);
        } catch (\Exception $e) {
            // If an exception occurs during the update process, catch it and return an error response
            \Log::error('Error updating image status: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error updating image status'], 500);
        }
    }
    
    public function listImages()
    {
        $images = Image::with(['status' => function ($query) {
            $query->orderByRaw("FIELD(status, 'Default', 'Unsure') DESC");
        }, 'user'])
        ->orderBy('image_status_id')
        ->get();
    

        return response()->json($images);
    }

}
