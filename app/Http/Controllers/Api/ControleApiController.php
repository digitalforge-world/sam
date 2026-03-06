<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Controle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControleApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Controle::with(['parcelle', 'producteur', 'culture', 'controleur']);
        
        if ($request->has('parcelle_id')) {
            $query->where('parcelle_id', $request->parcelle_id);
        }
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
            'parcelle_id' => 'required|exists:parcelles,id',
            'producteur_id' => 'required|exists:producteurs,id',
            'culture_id' => 'required|exists:cultures,id',
            'superficie_parcelle' => 'nullable|numeric|min:0',
            'superficie_bio' => 'nullable|numeric|min:0',
            'campagne' => 'required|string|max:20',
        ]);

        $validated['controleur_id'] = Auth::id();

        $controle = Controle::create($validated);
        
        return response()->json($controle->load(['parcelle', 'producteur', 'culture', 'controleur']), 201);
    }

    public function show(Controle $controle)
    {
        return response()->json($controle->load(['parcelle', 'producteur', 'culture', 'controleur']));
    }

    public function update(Request $request, Controle $controle)
    {
        $validated = $request->validate([
            'numero' => 'sometimes|required|string|max:50',
            'superficie_parcelle' => 'nullable|numeric|min:0',
            'superficie_bio' => 'nullable|numeric|min:0',
            'campagne' => 'sometimes|required|string|max:20',
        ]);

        $controle->update($validated);
        
        return response()->json($controle->load(['parcelle', 'producteur', 'culture', 'controleur']));
    }

    public function destroy(Controle $controle)
    {
        $controle->delete();
        return response()->json(null, 204);
    }
}
