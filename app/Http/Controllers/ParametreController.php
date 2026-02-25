<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $parametres = Parametre::orderBy('nom')->get();
        return view('parametres.index', compact('parametres'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'parametres'   => 'required|array',
            'parametres.*' => 'nullable|string|max:500',
        ]);

        foreach ($request->parametres as $id => $valeur) {
            Parametre::where('id', $id)->update(['valeur' => $valeur]);
        }

        return redirect()->route('parametres.index')->with('success', 'Paramètres mis à jour.');
    }
}
