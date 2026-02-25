<?php

namespace App\Http\Controllers;

use App\Models\OrganisationPaysanne;
use App\Models\Zone;
use App\Models\Village;
use App\Models\User;
use Illuminate\Http\Request;

class OrganisationPaysanneController extends Controller
{
    public function index()
    {
        $organisations = OrganisationPaysanne::with('zone', 'village', 'controleur')
            ->withCount('producteurs')->latest()->paginate(20);
        $zones = Zone::orderBy('nom')->get();
        return view('organisations.index', compact('organisations', 'zones'));
    }

    public function create()
    {
        $zones = Zone::orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        $controleurs = User::where('type', 'CONTROLEUR')->orderBy('name')->get();
        return view('organisations.create', compact('zones', 'villages', 'controleurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'zone_id' => 'required|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
            'controleur_id' => 'nullable|exists:users,id',
        ]);
        OrganisationPaysanne::create($data);
        return redirect()->route('organisations.index')->with('success', 'Organisation créée.');
    }

    public function edit(OrganisationPaysanne $organisation)
    {
        $zones = Zone::orderBy('nom')->get();
        $villages = Village::orderBy('nom')->get();
        $controleurs = User::where('type', 'CONTROLEUR')->orderBy('name')->get();
        return view('organisations.edit', compact('organisation', 'zones', 'villages', 'controleurs'));
    }

    public function update(Request $request, OrganisationPaysanne $organisation)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'zone_id' => 'required|exists:zones,id',
            'village_id' => 'required|exists:villages,id',
            'controleur_id' => 'nullable|exists:users,id',
        ]);
        $organisation->update($data);
        return redirect()->route('organisations.index')->with('success', 'Organisation modifiée.');
    }

    public function destroy(OrganisationPaysanne $organisation)
    {
        $organisation->delete();
        return redirect()->route('organisations.index')->with('success', 'Organisation supprimée.');
    }
}
