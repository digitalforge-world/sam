<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Identification;
use App\Models\Producteur;
use Illuminate\Http\Request;

class IdentificationController extends Controller
{
    public function index(Request $request)
    {
        $identifications = Identification::with(['producteur', 'controleur'])
            ->when($request->campagne, fn($q, $v) => $q->where('campagne', $v))
            ->when($request->statut, fn($q, $v) => $q->where('statut', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('identifications.index', compact('identifications'));
    }

    public function create()
    {
        $producteurs = Producteur::actif()->orderBy('nom')->get();
        return view('identifications.create', compact('producteurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:50',
            'producteur_id' => 'required|exists:producteurs,id',
            'superficie' => 'nullable|numeric|min:0',
            'campagne' => 'required|string|max:20',
            'approbation' => 'nullable|in:BIO,OK,DECLASSIFIED',
        ]);

        $data['controleur_id'] = auth()->id();

        Identification::create($data);
        return redirect()->route('identifications.index')->with('success', 'Identification créée.');
    }

    public function approve(Identification $identification, Request $request)
    {
        $data = $request->validate([
            'statut' => 'required|in:APPROUVE,REJETE',
            'approbation' => 'nullable|in:BIO,OK,DECLASSIFIED',
            'commentaire' => 'nullable|string',
        ]);

        $identification->update($data);
        
        $statusLabel = $data['statut'] === 'APPROUVE' ? 'approuvée' : 'rejetée';
        $message = "L'identification a été {$statusLabel} avec succès.";
        
        if ($data['statut'] === 'REJETE') {
            $message .= " Elle est renvoyée au contrôleur pour correction.";
        }

        return redirect()->route('identifications.index')->with('success', $message);
    }

    public function show(Identification $identification)
    {
        $identification->load(['producteur', 'controleur']);
        return view('identifications.show', compact('identification'));
    }

    public function edit(Identification $identification)
    {
        $producteurs = Producteur::actif()->orderBy('nom')->get();
        $cultures = \App\Models\Culture::orderBy('nom')->get();
        $villages = \App\Models\Village::orderBy('nom')->get();
        $organisations = \App\Models\OrganisationPaysanne::orderBy('nom')->get();
        return view('identifications.edit', compact('identification', 'producteurs', 'cultures', 'villages', 'organisations'));
    }

    public function update(Request $request, Identification $identification)
    {
        $data = $request->validate([
            'superficie' => 'nullable|numeric|min:0',
            'campagne' => 'required|string|max:20',
            'statut' => 'required|in:EN_ATTENTE,APPROUVE,REJETE',
            'culture_id' => 'nullable|exists:cultures,id',
            'village_id' => 'nullable|exists:villages,id',
            'organisation_id' => 'nullable|exists:organisation_paysannes,id',
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
        ]);
        
        $identification->update($data);
        return redirect()->route('identifications.show', $identification)->with('success', 'Identification modifiée avec succès.');
    }

    public function destroy(Identification $identification)
    {
        $identification->delete();
        return redirect()->route('identifications.index')->with('success', 'Identification supprimée.');
    }
}
