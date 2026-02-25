<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use App\Models\Producteur;
use App\Models\Village;
use App\Models\Culture;
use Illuminate\Http\Request;

class ParcelleController extends Controller
{
    public function index(Request $request)
    {
        $parcelles = Parcelle::query()
            ->with(['producteur', 'village', 'culture'])
            ->when($request->producteur_id, fn($q, $v) => $q->where('producteur_id', $v))
            ->when($request->bio, fn($q) => $q->where('bio', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('parcelles.index', compact('parcelles'));
    }

    public function create()
    {
        $producteurs = Producteur::actif()->orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        $cultures = Culture::orderBy('nom')->get();
        return view('parcelles.create', compact('producteurs', 'villages', 'cultures'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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
            'contour_geojson'        => 'nullable|json',
        ]);

        foreach (['bio', 'a_cours_eau', 'maisons_proximite', 'transformation_ferme'] as $field) {
            $data[$field] = $request->boolean($field);
        }

        if ($request->filled('contour_geojson')) {
            $geojson = json_decode($request->contour_geojson, true);
            if (isset($geojson['type']) && $geojson['type'] === 'Polygon') {
                $coords = $geojson['coordinates'][0];
                $data['contour'] = $geojson;
                $data['centre'] = [
                    'lat' => collect($coords)->avg(fn($c) => $c[1]),
                    'lng' => collect($coords)->avg(fn($c) => $c[0]),
                ];
            }
        }

        unset($data['contour_geojson']);
        $parcelle = Parcelle::create($data);

        return redirect()->route('parcelles.show', $parcelle)->with('success', 'Parcelle créée avec succès.');
    }

    public function show(Parcelle $parcelle)
    {
        $parcelle->load(['producteur.zone', 'village', 'culture', 'arbres.culture', 'controles']);
        return view('parcelles.show', compact('parcelle'));
    }

    public function edit(Parcelle $parcelle)
    {
        $producteurs = Producteur::actif()->orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        $cultures = Culture::orderBy('nom')->get();
        return view('parcelles.edit', compact('parcelle', 'producteurs', 'villages', 'cultures'));
    }

    public function update(Request $request, Parcelle $parcelle)
    {
        $data = $request->validate([
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
        ]);

        foreach (['bio', 'a_cours_eau', 'maisons_proximite', 'transformation_ferme'] as $field) {
            $data[$field] = $request->boolean($field);
        }

        $parcelle->update($data);
        return redirect()->route('parcelles.show', $parcelle)->with('success', 'Parcelle modifiée.');
    }

    public function destroy(Parcelle $parcelle)
    {
        $parcelle->delete();
        return redirect()->route('parcelles.index')->with('success', 'Parcelle supprimée.');
    }
}
