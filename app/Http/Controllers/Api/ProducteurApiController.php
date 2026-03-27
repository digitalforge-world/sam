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
            'nom'                    => 'required|string|max:100',
            'prenom'                 => 'required|string|max:100',
            'sexe'                   => 'sometimes|nullable|string|in:Masculin,Féminin,M,F',
            'telephone'              => 'nullable|string|max:20',
            'type_carte'             => 'nullable|string|max:100',
            'statut'                 => 'sometimes|nullable|string|in:Nouveau,Ancien,nouveau,ancien',
            'annee_adhesion'         => 'nullable|integer|min:1900|max:2100',
            'zone_id'                => 'nullable|exists:zones,id',
            'village_id'             => 'required|exists:villages,id',
            'organisation_paysanne_id' => 'nullable|exists:organisation_paysannes,id',
            'est_actif'              => 'boolean'
        ]);

        // Normaliser statut en minuscule pour correspondre à la DB (default 'nouveau')
        if (!empty($validated['statut'])) {
            $validated['statut'] = strtolower($validated['statut']);
        }

        $user = Auth::user();
        $validated['controleur_id'] = $user->id;

        // Récupérer zone_id depuis le compte utilisateur (controleur)
        if (empty($validated['zone_id'])) {
            $validated['zone_id'] = $user->zone_id;
        }

        // Si toujours vide → erreur claire
        if (empty($validated['zone_id'])) {
            return response()->json([
                'message' => 'Votre compte n\'est affecté à aucune zone. Veuillez contacter l\'administrateur.'
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
