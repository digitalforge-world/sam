<?php

namespace App\Http\Controllers;

use App\Models\Producteur;
use App\Models\Parcelle;
use App\Models\Identification;
use App\Models\Zone;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $stats = [
            'producteurs'      => Producteur::actif()->count(),
            'parcelles_bio'    => Parcelle::where('bio', true)->count(),
            'superficie_totale'=> Parcelle::where('bio', true)->sum('superficie_bio') ?? 0,
            'en_attente'       => Identification::where('statut', 'EN_ATTENTE')->count(),
            'total_parcelles'  => Parcelle::count(),
            'total_zones'      => Zone::count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
