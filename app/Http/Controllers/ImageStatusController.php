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
}
