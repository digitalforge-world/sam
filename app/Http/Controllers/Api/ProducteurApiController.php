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
        
        // ISOLATION : Un contrôleur ne voit que ses propres producteurs
        if ($request->user()) {
            $query->where('controleur_id', $request->user()->id);
        }

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
            'sexe' => 'sometimes|nullable|string|in:Masculin,Féminin',
            'telephone' => 'nullable|string|max:20',
            'type_carte' => 'nullable|string|max:100',
            'statut' => 'sometimes|nullable|string|in:Nouveau,Ancien',
            'annee_adhesion' => 'required_if:statut,Ancien|nullable|integer',
            'zone_id' => 'nullable|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
            'organisation_paysanne_id' => 'nullable|exists:organisation_paysannes,id',
            'est_actif' => 'boolean'
        ]);

        $user = Auth::user();
        $validated['controleur_id'] = $user->id;
        
        // Sécurité : Si la zone n'est pas fournie, on tente de prendre celle du controleur
        if (empty($validated['zone_id'])) {
            $validated['zone_id'] = $user->zone_id;
        }

        // Si après l'assignation automatique la zone est toujours vide, on affiche une erreur claire
        if (empty($validated['zone_id'])) {
            return response()->json([
                'message' => 'Veuillez contacter l\'administrateur : votre compte n\'est affecté à aucune zone de travail.'
            ], 422);
        }
        
        $producteur = Producteur::create($validated);
        
        return response()->json($producteur->load(['zone', 'village', 'organisation', 'controleur']), 201);
    }

    public function show(Producteur $producteur)
    {
        if ($producteur->controleur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
        return response()->json($producteur->load(['zone', 'village', 'organisation', 'controleur', 'parcelles']));
    }

    public function update(Request $request, Producteur $producteur)
    {
        if ($producteur->controleur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:100',
            'prenom' => 'sometimes|required|string|max:100',
            'sexe' => 'sometimes|nullable|string|in:Masculin,Féminin',
            'telephone' => 'nullable|string|max:20',
            'type_carte' => 'nullable|string|max:100',
            'statut' => 'sometimes|nullable|string|in:Nouveau,Ancien',
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
        if ($producteur->controleur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
        $producteur->delete();
        return response()->json(null, 204);
    }
}
