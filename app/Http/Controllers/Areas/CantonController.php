<?php

namespace App\Http\Controllers\Areas;

use App\Http\Controllers\Controller;
use App\Models\Canton;
use App\Models\Region;
use App\Models\Prefecture;
use Illuminate\Http\Request;

class CantonController extends Controller
{
    public function index()
    {
        $cantons = Canton::with('region', 'prefecture')->withCount('villages')->latest()->paginate(20);
        return view('areas.cantons.index', compact('cantons'));
    }

    public function create()
    {
        $regions = Region::orderBy('nom')->get();
        $prefectures = Prefecture::orderBy('nom')->get();
        return view('areas.cantons.create', compact('regions', 'prefectures'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'nom' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
        ]);
        Canton::create($data);
        return redirect()->route('areas.cantons.index')->with('success', 'Canton créé avec succès.');
    }

    public function edit(Canton $canton)
    {
        $regions = Region::orderBy('nom')->get();
        $prefectures = Prefecture::orderBy('nom')->get();
        return view('areas.cantons.edit', compact('canton', 'regions', 'prefectures'));
    }

    public function update(Request $request, Canton $canton)
    {
        $data = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'prefecture_id' => 'required|exists:prefectures,id',
            'nom' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
        ]);
        $canton->update($data);
        return redirect()->route('areas.cantons.index')->with('success', 'Canton modifié.');
    }

    public function destroy(Canton $canton)
    {
        $canton->delete();
        return redirect()->route('areas.cantons.index')->with('success', 'Canton supprimé.');
    }

    public function filter(Request $request)
    {
        return Canton::when($request->prefecture_id, fn($q, $v) => $q->where('prefecture_id', $v))
            ->get(['id', 'nom'])
            ->map(fn($c) => ['value' => $c->id, 'label' => $c->nom]);
    }
}
