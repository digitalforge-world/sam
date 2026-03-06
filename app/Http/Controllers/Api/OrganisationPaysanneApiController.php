<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganisationPaysanne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganisationPaysanneApiController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganisationPaysanne::with(['zone', 'village', 'controleur']);
        
        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }
        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:150',
            'zone_id' => 'required|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
        ]);

        $validated['controleur_id'] = Auth::id();

        $op = OrganisationPaysanne::create($validated);
        return response()->json($op->load(['zone', 'village', 'controleur']), 201);
    }

    public function show(OrganisationPaysanne $organisation)
    {
        return response()->json($organisation->load(['zone', 'village', 'controleur', 'producteurs']));
    }

    public function update(Request $request, OrganisationPaysanne $organisation)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:150',
            'zone_id' => 'sometimes|required|exists:zones,id',
            'village_id' => 'sometimes|required|exists:villages,id',
        ]);

        $organisation->update($validated);
        
        return response()->json($organisation->load(['zone', 'village', 'controleur']));
    }

    public function destroy(OrganisationPaysanne $organisation)
    {
        $organisation->delete();
        return response()->json(null, 204);
    }
}
