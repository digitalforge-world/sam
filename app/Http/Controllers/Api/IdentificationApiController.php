<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Identification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdentificationApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Identification::with(['producteur', 'controleur']);
        
        if ($request->has('producteur_id')) {
            $query->where('producteur_id', $request->producteur_id);
        }
        if ($request->has('campagne')) {
            $query->where('campagne', $request->campagne);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:50',
            'producteur_id' => 'required|exists:producteurs,id',
            'superficie' => 'nullable|numeric|min:0',
            'campagne' => 'required|string|max:20',
        ]);

        $validated['controleur_id'] = Auth::id();

        $identification = Identification::create($validated);
        
        return response()->json($identification->load(['producteur', 'controleur']), 201);
    }

    public function show(Identification $identification)
    {
        return response()->json($identification->load(['producteur', 'controleur']));
    }

    public function update(Request $request, Identification $identification)
    {
        $validated = $request->validate([
            'numero' => 'sometimes|required|string|max:50',
            'superficie' => 'nullable|numeric|min:0',
            'statut' => 'sometimes|required|in:EN_ATTENTE,APPROUVE,REJETE',
            'approbation' => 'nullable|in:BIO,OK,DECLASSIFIED',
            'campagne' => 'sometimes|required|string|max:20',
        ]);

        $identification->update($validated);
        
        return response()->json($identification->load(['producteur', 'controleur']));
    }

    public function destroy(Identification $identification)
    {
        $identification->delete();
        return response()->json(null, 204);
    }
}
