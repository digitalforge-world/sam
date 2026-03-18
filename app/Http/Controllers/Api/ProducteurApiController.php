<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProducteurApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Producteur::with(['zone', 'village', 'organisation', 'controleur']);
        
        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|string|in:Masculin,Féminin',
            'telephone' => 'nullable|string|max:20',
            'type_carte' => 'nullable|string|max:100',
            'statut' => 'required|string|in:Nouveau,Ancien',
            'annee_adhesion' => 'required_if:statut,Ancien|nullable|integer',
            'zone_id' => 'required|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
            'organisation_paysanne_id' => 'nullable|exists:organisation_paysannes,id',
            'est_actif' => 'boolean'
        ]);

        $validated['controleur_id'] = Auth::id();
        
        $producteur = Producteur::create($validated);
        
        return response()->json($producteur->load(['zone', 'village', 'organisation', 'controleur']), 201);
    }

    public function show(Producteur $producteur)
    {
        return response()->json($producteur->load(['zone', 'village', 'organisation', 'controleur', 'parcelles']));
    }

    public function update(Request $request, Producteur $producteur)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:100',
            'prenom' => 'sometimes|required|string|max:100',
            'sexe' => 'sometimes|required|string|in:Masculin,Féminin',
            'telephone' => 'nullable|string|max:20',
            'type_carte' => 'nullable|string|max:100',
            'statut' => 'sometimes|required|string|in:Nouveau,Ancien',
            'annee_adhesion' => 'required_if:statut,Ancien|nullable|integer',
            'zone_id' => 'sometimes|required|exists:zones,id',
            'village_id' => 'sometimes|required|exists:villages,id',
            'organisation_paysanne_id' => 'nullable|exists:organisation_paysannes,id',
            'est_actif' => 'boolean'
        ]);

        $producteur->update($validated);
        
        return response()->json($producteur->load(['zone', 'village', 'organisation', 'controleur']));
    }

    public function destroy(Producteur $producteur)
    {
        $producteur->delete();
        return response()->json(null, 204);
    }
}
