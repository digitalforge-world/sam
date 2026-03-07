<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Region;
use App\Models\Prefecture;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $communes = Commune::with(['region', 'prefecture'])->latest()->paginate(15);
        return view('areas.communes.index', compact('communes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::all();
        return view('areas.communes.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'nom' => 'required|string|max:255',
        ]);

        Commune::create($validated);

        return redirect()->route('areas.communes.index')->with('success', 'Commune créée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commune $commune)
    {
        $regions = Region::all();
        $prefectures = Prefecture::where('region_id', $commune->region_id)->get();
        return view('areas.communes.edit', compact('commune', 'regions', 'prefectures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commune $commune)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'nom' => 'required|string|max:255',
        ]);

        $commune->update($validated);

        return redirect()->route('areas.communes.index')->with('success', 'Commune mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commune $commune)
    {
        $commune->delete();
        return redirect()->route('areas.communes.index')->with('success', 'Commune supprimée avec succès.');
    }
}
