<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use Illuminate\Http\Request;

class CultureController extends Controller
{
    public function index()
    {
        $cultures = Culture::latest()->paginate(20);
        return view('cultures.index', compact('cultures'));
    }

    public function create()
    {
        return view('cultures.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nom' => 'required|string|max:100|unique:cultures,nom']);
        Culture::create($data);
        return redirect()->route('cultures.index')->with('success', 'Culture créée.');
    }

    public function edit(Culture $culture)
    {
        return view('cultures.edit', compact('culture'));
    }

    public function update(Request $request, Culture $culture)
    {
        $data = $request->validate(['nom' => 'required|string|max:100|unique:cultures,nom,' . $culture->id]);
        $culture->update($data);
        return redirect()->route('cultures.index')->with('success', 'Culture modifiée.');
    }

    public function destroy(Culture $culture)
    {
        $culture->delete();
        return redirect()->route('cultures.index')->with('success', 'Culture supprimée.');
    }
}
