<?php

namespace App\Http\Controllers;

use App\Models\ImageStatus;
use Illuminate\Http\Request;

class ImageStatusController extends Controller
{
    public function getImageStatus()
    {
        $imageStatuses = ImageStatus::where('active', 1)
            ->whereNotIn('status', ['Default', 'Unsure'])
            ->pluck('status');
    
        return response()->json(['imageStatuses' => $imageStatuses]);
    }

    public function listImageStatus()
    {
        $imageStatus = ImageStatus::get();
    
        return response()->json($imageStatus);
    }

    public function addImageStatus(Request $request) {

        // Check if there are already 2 active entries, ignoring default and unsure status
        $activeCount = ImageStatus::where('active', 1)
        ->whereNotIn('status', ['Default', 'Unsure'])
        ->count();
    
        // Mark the new entry as inactive if 2 entries are already active
        $newStatusData = [
            'status' => $request->input('status'), // Assuming 'name' is the field for status name
            'active' => ($activeCount >= 2) ? 0 : $request->input('active'),
        ];
    
        // Create a new image status using the create method
        ImageStatus::create($newStatusData);

        // Check if 2 entries are already active
        if ($activeCount >= 2) {
            return response()->json(['message' => '2 entries are already active'], 200);
        }
    
        // Return success response
        return response()->json(['message' => 'Image status added successfully'], 200);
    }
}
