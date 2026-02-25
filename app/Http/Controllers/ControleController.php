<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Parcelle;
use App\Models\Culture;
use App\Models\Producteur;
use Illuminate\Http\Request;

class ControleController extends Controller
{
    public function index(Request $request)
    {
        $controles = Controle::with(['parcelle', 'producteur', 'culture', 'controleur'])
            ->when($request->campagne, fn($q, $v) => $q->where('campagne', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('controles.index', compact('controles'));
    }

    public function create()
    {
        $parcelles = Parcelle::with('producteur')->orderBy('id', 'desc')->get();
        $cultures = Culture::orderBy('nom')->get();
        $producteurs = Producteur::actif()->orderBy('nom')->get();
        return view('controles.create', compact('parcelles', 'cultures', 'producteurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:50',
            'parcelle_id' => 'required|exists:parcelles,id',
            'producteur_id' => 'required|exists:producteurs,id',
            'culture_id' => 'required|exists:cultures,id',
            'superficie_parcelle' => 'nullable|numeric|min:0',
            'superficie_bio' => 'nullable|numeric|min:0',
            'campagne' => 'required|string|max:20',
        ]);

        $data['controleur_id'] = auth()->id();

        Controle::create($data);
        return redirect()->route('controles.index')->with('success', 'Contrôle créé.');
    }
}
