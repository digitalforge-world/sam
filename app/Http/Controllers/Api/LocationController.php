<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Prefecture;
use App\Models\Canton;
use App\Models\Village;
use App\Models\Zone;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function regions()
    {
        return response()->json(Region::all());
    }

    public function prefectures(Request $request)
    {
        $query = Prefecture::query();
        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        return response()->json($query->get());
    }

    public function communes(Request $request)
    {
        $query = \App\Models\Commune::query();
        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->has('prefecture_id')) {
            $query->where('prefecture_id', $request->prefecture_id);
        }
        return response()->json($query->get());
    }

    public function cantons(Request $request)
    {
        $query = Canton::query();
        if ($request->has('prefecture_id')) {
            $query->where('prefecture_id', $request->prefecture_id);
        }
        return response()->json($query->get());
    }

    public function villages(Request $request)
    {
        $query = Village::query();
        if ($request->has('canton_id')) {
            $query->where('canton_id', $request->canton_id);
        }
        return response()->json($query->get());
    }

    public function zones()
    {
        // Don't send passwords in the Zone model, it's already hidden in the model
        return response()->json(Zone::all());
    }
}
