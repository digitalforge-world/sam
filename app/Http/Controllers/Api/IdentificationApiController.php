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
        
        // ISOLATION : Un contrôleur ne voit que ses propres dossiers
        if ($request->user()) {
            $query->where('controleur_id', $request->user()->id);
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
            //'numero' => 'required|string|max:50', // We can autogenerate it or make it nullable for now
            'numero' => 'nullable|string|max:50',
            'producteur_id' => 'required|exists:producteurs,id',
            'superficie' => 'nullable|numeric|min:0',
            'campagne' => 'nullable|string|max:20', // the user didn't mention it, make it nullable
            
            'culture_id' => 'nullable|exists:cultures,id',
            'village' => 'nullable|string',
            'organisation_paysanne' => 'nullable|string',
            'statut_producteur' => 'nullable|string',
            'nom_parcelle' => 'nullable|string',
            
            'participation_formations' => 'boolean',
            'production_parallele' => 'boolean',
            'diversite_biologique' => 'boolean',
            'gestion_dechets' => 'boolean',
            'emballage_non_conforme' => 'boolean',
            'rotation_cultures' => 'boolean',
            'isolement_parcelles' => 'boolean',
            'preparation_sol' => 'boolean',
            'fertilisation' => 'boolean',
            'semences' => 'boolean',
            'gestion_adventices' => 'boolean',
            'gestion_ravageurs' => 'boolean',
            'recolte' => 'boolean',
            'stockage' => 'boolean',
            
            'commentaire' => 'nullable|string',

            'date_preparation_sol' => 'nullable|date',
            'date_semis' => 'nullable|date',
            'date_sarclage_1' => 'nullable|date',
            'date_sarclage_2' => 'nullable|date',
            'date_fertilisation' => 'nullable|date',
            'date_recolte' => 'nullable|date',

            'arbres' => 'nullable|array',
            'niveau_pente' => 'nullable|string',
            'type_culture' => 'nullable|string',
            'a_cours_eau' => 'boolean',
            'maisons_environnantes' => 'boolean',
            'cultures_proximite' => 'nullable|string',
            'rencontre_avec' => 'nullable|string',
            'photo_parcelle' => 'nullable|string',
            'signature_producteur' => 'nullable|string',
            'coordonnees_polygon' => 'nullable|array',
        ]);
        
        if (empty($validated['numero'])) {
            $validated['numero'] = 'ID-' . strtoupper(uniqid());
        }
        if (empty($validated['campagne'])) {
            $validated['campagne'] = date('Y') . '-' . (date('Y') + 1);
        }

        $validated['controleur_id'] = Auth::id();

        $identification = Identification::create($validated);
        
        return response()->json($identification->load(['producteur', 'controleur']), 201);
    }

    public function show(Identification $identification)
    {
        if ($identification->controleur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
        return response()->json($identification->load(['producteur', 'controleur']));
    }

    public function update(Request $request, Identification $identification)
    {
        if ($identification->controleur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

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
        if ($identification->controleur_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }
        $identification->delete();
        return response()->json(null, 204);
    }
}
