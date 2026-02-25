<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\Region;
use App\Models\Prefecture;
use App\Models\Canton;
use App\Models\User;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index()
    {
        $villages = Village::with('region', 'prefecture', 'canton', 'controleur')
            ->withCount('producteurs')->latest()->paginate(20);
        return view('areas.villages.index', compact('villages'));
    }

    public function create()
    {
        $regions = Region::orderBy('nom')->get();
        $prefectures = Prefecture::orderBy('nom')->get();
        $cantons = Canton::orderBy('nom')->get();
        $controleurs = User::where('type', 'CONTROLEUR')->orderBy('name')->get();
        return view('areas.villages.create', compact('regions', 'prefectures', 'cantons', 'controleurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'canton_id' => 'required|exists:cantons,id',
            'controleur_id' => 'nullable|exists:users,id',
            'nom' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
        ]);
        Village::create($data);
        return redirect()->route('areas.villages.index')->with('success', 'Village créé avec succès.');
    }

    public function edit(Village $village)
    {
        $regions = Region::orderBy('nom')->get();
        $prefectures = Prefecture::orderBy('nom')->get();
        $cantons = Canton::orderBy('nom')->get();
        $controleurs = User::where('type', 'CONTROLEUR')->orderBy('name')->get();
        return view('areas.villages.edit', compact('village', 'regions', 'prefectures', 'cantons', 'controleurs'));
    }

    public function update(Request $request, Village $village)
    {
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'canton_id' => 'required|exists:cantons,id',
            'controleur_id' => 'nullable|exists:users,id',
            'nom' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
        ]);
        $village->update($data);
        return redirect()->route('areas.villages.index')->with('success', 'Village modifié.');
    }

    public function destroy(Village $village)
    {
        $village->delete();
        return redirect()->route('areas.villages.index')->with('success', 'Village supprimé.');
    }

    public function filter(Request $request)
    {
        return Village::when($request->canton_id, fn($q, $v) => $q->where('canton_id', $v))
            ->when($request->zone_id, fn($q, $v) => $q->where('zone', $v))
            ->get(['id', 'nom'])
            ->map(fn($v) => ['value' => $v->id, 'label' => $v->nom]);
    }
}
