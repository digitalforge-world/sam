<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount('prefectures', 'cantons', 'villages')->latest()->paginate(20);
        return view('areas.regions.index', compact('regions'));
    }

    public function create()
    {
        return view('areas.regions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nom' => 'required|string|max:100|unique:regions,nom']);
        Region::create($data);
        return redirect()->route('areas.regions.index')->with('success', 'Région créée avec succès.');
    }

    public function edit(Region $region)
    {
        return view('areas.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $data = $request->validate(['nom' => 'required|string|max:100|unique:regions,nom,' . $region->id]);
        $region->update($data);
        return redirect()->route('areas.regions.index')->with('success', 'Région modifiée avec succès.');
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return redirect()->route('areas.regions.index')->with('success', 'Région supprimée.');
    }
}
