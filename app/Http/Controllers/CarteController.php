<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use App\Models\Producteur;
use App\Models\Zone;
use Illuminate\Http\Request;

class CarteController extends Controller
{
    public function index()
    {
        $zones = Zone::orderBy('nom')->get(['id', 'nom']);
        $producteurs = Producteur::actif()->orderBy('nom')->get(['id', 'nom', 'prenom', 'code']);

        return view('carte.index', [
            'zones'       => $zones,
            'producteurs' => $producteurs,
            'mapboxToken' => config('services.mapbox.token'),
        ]);
    }

    public function geojson(Request $request)
    {
        $parcelles = Parcelle::query()
            ->with(['producteur:id,nom,prenom,code', 'culture:id,nom', 'village:id,nom'])
            ->whereNotNull('contour')
            ->when($request->zone_id, fn($q, $v) => $q->whereHas('producteur', fn($q) => $q->withoutGlobalScopes()->where('zone_id', $v)))
            ->when($request->producteur_id, fn($q, $v) => $q->where('producteur_id', $v))
            ->when($request->bio, fn($q) => $q->where('bio', true))
            ->get();

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $parcelles->map(fn(Parcelle $p) => [
                'type'       => 'Feature',
                'id'         => $p->id,
                'geometry'   => $p->contour,
                'properties' => [
                    'id'              => $p->id,
                    'indice'          => $p->indice,
                    'producteur'      => "{$p->producteur->nom} {$p->producteur->prenom}",
                    'code_producteur' => $p->producteur->code,
                    'village'         => $p->village?->nom,
                    'culture'         => $p->culture?->nom,
                    'superficie'      => $p->superficie,
                    'superficie_bio'  => $p->superficie_bio,
                    'bio'             => $p->bio,
                    'approbation'     => $p->approbation_production,
                    'couleur'         => $p->couleur_carte,
                ],
            ])
        ]);
    }

    public function saveContour(Request $request)
    {
        $request->validate([
            'parcelle_id' => 'required|exists:parcelles,id',
            'contour'     => 'required',
        ]);

        $parcelle = Parcelle::findOrFail($request->parcelle_id);
        $coords   = json_decode($request->contour, true);

        $geojson = ['type' => 'Polygon', 'coordinates' => $coords];

        $flat = $coords[0] ?? $coords;
        $parcelle->update([
            'contour' => $geojson,
            'centre'  => [
                'lat' => collect($flat)->avg(fn($c) => $c[1]),
                'lng' => collect($flat)->avg(fn($c) => $c[0]),
            ],
        ]);

        return response()->json(['success' => true, 'message' => 'Contour sauvegardé.']);
    }

    public function deleteContour(Parcelle $parcelle)
    {
        $parcelle->update(['contour' => null, 'centre' => null]);
        return response()->json(['success' => true]);
    }
}
