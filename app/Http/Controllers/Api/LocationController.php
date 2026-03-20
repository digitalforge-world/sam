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

    /**
     * Store a newly created village.
     */
    public function store_village(Request $request)
    {
        $request->validate([
            'region_id'    => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'commune_id'   => 'required|exists:communes,id',
            'canton_id'    => 'required|exists:cantons,id',
            'nom'          => 'required|string|max:255',
        ]);

        $village = Village::create([
            'region_id'    => $request->region_id,
            'prefecture_id' => $request->prefecture_id,
            'commune_id'   => $request->commune_id,
            'canton_id'    => $request->canton_id,
            'nom'          => $request->nom,
            'zone'         => $request->zone ?? null,
        ]);

        return response()->json([
            'message' => 'Village créé avec succès',
            'village' => $village
        ], 201);
    }

    public function zones()
    {
        // Don't send passwords in the Zone model, it's already hidden in the model
        return response()->json(Zone::all());
    }
}
