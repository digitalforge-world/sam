<?php

namespace App\Http\Controllers;

use App\Models\Arbre;
use App\Models\Culture;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class ArbreController extends Controller
{
    public function store(Request $request, Parcelle $parcelle)
    {
        $data = $request->validate([
            'culture_id' => 'required|exists:cultures,id',
            'nombre' => 'required|integer|min:1',
        ]);
        $data['parcelle_id'] = $parcelle->id;
        Arbre::create($data);
        return redirect()->route('parcelles.show', $parcelle)->with('success', 'Arbre ajouté.');
    }

    public function destroy(Arbre $arbre)
    {
        $parcelle = $arbre->parcelle;
        $arbre->delete();
        return redirect()->route('parcelles.show', $parcelle)->with('success', 'Arbre supprimé.');
    }
}
