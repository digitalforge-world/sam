<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcelle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelleApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Parcelle::with(['producteur', 'village', 'culture', 'arbres', 'controles']);
        
        if ($request->has('producteur_id')) {
            $query->where('producteur_id', $request->producteur_id);
        }
        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }
        if ($request->has('culture_id')) {
            $query->where('culture_id', $request->culture_id);
        }
        if ($request->has('bio')) {
            $query->where('bio', $request->boolean('bio'));
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'producteur_id'          => 'required|exists:producteurs,id',
            'village_id'             => 'nullable|exists:villages,id',
            'culture_id'             => 'nullable|exists:cultures,id',
            'superficie'             => 'nullable|numeric|min:0',
            'superficie_bio'         => 'nullable|numeric|min:0',
            'rendement_bio'          => 'nullable|numeric|min:0',
            'volume_production'      => 'nullable|integer|min:0',
            'niveau_pente'           => 'nullable|in:WITHOUT,SMALL,MEDIUM,HIGH',
            'type_culture'           => 'nullable|in:SINGLE,ASSOCIATIVE,SPACER,PURE,STOLEN',
            'type_employes'          => 'nullable|in:SEASONAL,PERMANENT',
            'approbation_production' => 'nullable|in:BIO,OK,DECLASSIFIED',
            'bio'                    => 'boolean',
            'a_cours_eau'            => 'boolean',
            'maisons_proximite'      => 'boolean',
            'transformation_ferme'   => 'boolean',
            'contour_geojson'        => 'nullable|json',
        ]);

        $parcelle = DB::transaction(function () use ($validated) {
            $contourGeojson = null;
            if (isset($validated['contour_geojson'])) {
                $contourGeojson = $validated['contour_geojson'];
                unset($validated['contour_geojson']);
            }

            $parcelle = Parcelle::create($validated);

            if ($contourGeojson) {
                // The geometry will be parsed by SpatialTrait or handled properly if we convert it
                // Using Grimzy\LaravelMysqlSpatial helper or raw query if needed
                // For now assuming we can update the spatial field via raw expression if needed
                $parcelle->contour = \Grimzy\LaravelMysqlSpatial\Types\Geometry::fromJson($contourGeojson);
                // Also calculate center and save
                $parcelle->save();
            }

            return $parcelle;
        });

        return response()->json($parcelle->load(['producteur', 'village', 'culture']), 201);
    }

    public function show(Parcelle $parcelle)
    {
        return response()->json($parcelle->load(['producteur', 'village', 'culture', 'arbres', 'controles']));
    }

    public function update(Request $request, Parcelle $parcelle)
    {
        $validated = $request->validate([
            'village_id'             => 'nullable|exists:villages,id',
            'culture_id'             => 'nullable|exists:cultures,id',
            'superficie'             => 'nullable|numeric|min:0',
            'superficie_bio'         => 'nullable|numeric|min:0',
            'rendement_bio'          => 'nullable|numeric|min:0',
            'volume_production'      => 'nullable|integer|min:0',
            'niveau_pente'           => 'nullable|in:WITHOUT,SMALL,MEDIUM,HIGH',
            'type_culture'           => 'nullable|in:SINGLE,ASSOCIATIVE,SPACER,PURE,STOLEN',
            'type_employes'          => 'nullable|in:SEASONAL,PERMANENT',
            'approbation_production' => 'nullable|in:BIO,OK,DECLASSIFIED',
            'bio'                    => 'boolean',
            'a_cours_eau'            => 'boolean',
            'maisons_proximite'      => 'boolean',
            'transformation_ferme'   => 'boolean',
            'contour_geojson'        => 'nullable|json',
        ]);

        DB::transaction(function () use ($parcelle, $validated) {
            if (isset($validated['contour_geojson'])) {
                $parcelle->contour = \Grimzy\LaravelMysqlSpatial\Types\Geometry::fromJson($validated['contour_geojson']);
                unset($validated['contour_geojson']);
            }
            
            $parcelle->update($validated);
        });

        return response()->json($parcelle->load(['producteur', 'village', 'culture']));
    }

    public function destroy(Parcelle $parcelle)
    {
        $parcelle->delete();
        return response()->json(null, 204);
    }
}
