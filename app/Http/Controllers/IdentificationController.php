<?php

namespace App\Http\Controllers;

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
        ]);

        $identification->update($data);
        return redirect()->back()->with('success', 'Identification mise à jour.');
    }
}
