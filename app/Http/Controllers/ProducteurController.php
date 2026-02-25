<?php

namespace App\Http\Controllers;

use App\Models\Producteur;
use App\Models\Zone;
use App\Models\Village;
use App\Models\OrganisationPaysanne;
use App\Models\User;
use Illuminate\Http\Request;

class ProducteurController extends Controller
{
    public function index(Request $request)
    {
        $producteurs = Producteur::query()
            ->with(['zone', 'village', 'organisation', 'controleur'])
            ->withCount('parcelles')
            ->when($request->zone_id, fn($q, $v) => $q->where('zone_id', $v))
            ->when($request->search, fn($q, $s) => $q->where(function($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            }))
            ->when($request->has('est_actif') && $request->est_actif !== '', fn($q) => $q->where('est_actif', $request->est_actif))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $zones = Zone::orderBy('nom')->get();
        return view('producteurs.index', compact('producteurs', 'zones'));
    }

    public function create()
    {
        $zones = Zone::orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        $organisations = OrganisationPaysanne::orderBy('nom')->get();
        $controleurs = User::where('type', 'CONTROLEUR')->orderBy('name')->get();
        return view('producteurs.create', compact('zones', 'villages', 'organisations', 'controleurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'zone_id' => 'required|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
            'organisation_paysanne_id' => 'nullable|exists:organisation_paysannes,id',
            'controleur_id' => 'nullable|exists:users,id',
        ]);
        $producteur = Producteur::create($data);
        return redirect()->route('producteurs.show', $producteur)->with('success', 'Producteur créé avec succès.');
    }

    public function show(Producteur $producteur)
    {
        $producteur->load(['zone', 'village', 'organisation', 'controleur', 'parcelles.culture', 'identifications']);
        return view('producteurs.show', compact('producteur'));
    }

    public function edit(Producteur $producteur)
    {
        $zones = Zone::orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        $organisations = OrganisationPaysanne::orderBy('nom')->get();
        $controleurs = User::where('type', 'CONTROLEUR')->orderBy('name')->get();
        return view('producteurs.edit', compact('producteur', 'zones', 'villages', 'organisations', 'controleurs'));
    }

    public function update(Request $request, Producteur $producteur)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'zone_id' => 'required|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
            'organisation_paysanne_id' => 'nullable|exists:organisation_paysannes,id',
            'controleur_id' => 'nullable|exists:users,id',
            'est_actif' => 'boolean',
        ]);
        $data['est_actif'] = $request->boolean('est_actif');
        $producteur->update($data);
        return redirect()->route('producteurs.show', $producteur)->with('success', 'Producteur modifié.');
    }

    public function destroy(Producteur $producteur)
    {
        $producteur->delete();
        return redirect()->route('producteurs.index')->with('success', 'Producteur supprimé.');
    }

    public function filter(Request $request)
    {
        return Producteur::actif()
            ->when($request->zone_id, fn($q, $v) => $q->where('zone_id', $v))
            ->when($request->village_id, fn($q, $v) => $q->where('village_id', $v))
            ->get(['id', 'nom', 'prenom', 'code'])
            ->map(fn($p) => ['value' => $p->id, 'label' => "{$p->code} — {$p->nom} {$p->prenom}"]);
    }
}
