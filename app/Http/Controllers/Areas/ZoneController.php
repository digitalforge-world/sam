<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::withCount('producteurs', 'organisations', 'users')->latest()->paginate(20);
        return view('areas.zones.index', compact('zones'));
    }

    public function create()
    {
        return view('areas.zones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:100|unique:zones,nom',
            'mot_de_passe' => 'required|string|min:4',
        ]);
        Zone::create($data);
        return redirect()->route('areas.zones.index')->with('success', 'Zone créée avec succès.');
    }

    public function edit(Zone $zone)
    {
        return view('areas.zones.edit', compact('zone'));
    }

    public function update(Request $request, Zone $zone)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:100|unique:zones,nom,' . $zone->id,
            'mot_de_passe' => 'nullable|string|min:4',
        ]);
        if (empty($data['mot_de_passe'])) unset($data['mot_de_passe']);
        $zone->update($data);
        return redirect()->route('areas.zones.index')->with('success', 'Zone modifiée.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return redirect()->route('areas.zones.index')->with('success', 'Zone supprimée.');
    }
}
