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
        // Check if an 'id' is present in the request
        $id = $request->input('id');
        $active = intval($request->input('active'));
    
        // Check if there are already 2 active entries, ignoring default and unsure status
        $activeCount = ImageStatus::where('active', 1)
            ->whereNotIn('status', ['Default', 'Unsure'])
            ->count();
    
        // Mark the new entry as inactive if 2 entries are already active
        $newStatusData = [
            'status' => $request->input('status'), // Assuming 'name' is the field for status name
            'active' => ($activeCount >= 2) ? 0 : $request->input('active'),
        ];
    
        if ($id) {
            // If an 'id' is provided, update the existing entry
            $imageStatus = ImageStatus::find($id);

            // If entry is being made active when there are already 2 active entries
            if($active === 1 && $activeCount >= 2 && $imageStatus->active === 0) {
                return response()->json(['message' => '2 entries are already active'], 200);
            }
            
            if ($imageStatus) {
                $imageStatus->update($newStatusData);
                return response()->json(['message' => 'Image status updated successfully'], 200);
            } else {
                return response()->json(['error' => 'Image status not found for the provided ID'], 404);
            }
        } else {
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

    public function editImageStatus($id)
    {
        try {
            $imageStatus = ImageStatus::findOrFail($id);
            // Assuming 'status' and 'active' are columns in your ImageStatus model
            $data = [
                'status' => $imageStatus->status,
                'active' => $imageStatus->active,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image status not found.'], 404);
        }

    }
}
