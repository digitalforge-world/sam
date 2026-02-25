<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Models\Prefecture;
use App\Models\Region;
use Illuminate\Http\Request;

class PrefectureController extends Controller
{
    public function index()
    {
        $prefectures = Prefecture::with('region')->withCount('cantons', 'villages')->latest()->paginate(20);
        return view('areas.prefectures.index', compact('prefectures'));
    }

    public function create()
    {
        $regions = Region::orderBy('nom')->get();
        return view('areas.prefectures.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'nom' => 'required|string|max:100',
        ]);
        Prefecture::create($data);
        return redirect()->route('areas.prefectures.index')->with('success', 'Préfecture créée avec succès.');
    }

    public function edit(Prefecture $prefecture)
    {
        $regions = Region::orderBy('nom')->get();
        return view('areas.prefectures.edit', compact('prefecture', 'regions'));
    }

    public function update(Request $request, Prefecture $prefecture)
    {
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'nom' => 'required|string|max:100',
        ]);
        $prefecture->update($data);
        return redirect()->route('areas.prefectures.index')->with('success', 'Préfecture modifiée.');
    }

    public function destroy(Prefecture $prefecture)
    {
        $prefecture->delete();
        return redirect()->route('areas.prefectures.index')->with('success', 'Préfecture supprimée.');
    }
}
