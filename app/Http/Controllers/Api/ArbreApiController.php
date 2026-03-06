<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Arbre;
use Illuminate\Http\Request;

class ArbreApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Arbre::with(['parcelle', 'culture']);
        
        if ($request->has('parcelle_id')) {
            $query->where('parcelle_id', $request->parcelle_id);
        }
        if ($request->has('culture_id')) {
            $query->where('culture_id', $request->culture_id);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parcelle_id' => 'required|exists:parcelles,id',
            'culture_id' => 'required|exists:cultures,id',
            'nombre' => 'required|integer|min:1',
        ]);

        $arbre = Arbre::create($validated);
        
        return response()->json($arbre->load(['parcelle', 'culture']), 201);
    }

    public function show(Arbre $arbre)
    {
        return response()->json($arbre->load(['parcelle', 'culture']));
    }

    public function update(Request $request, Arbre $arbre)
    {
        $validated = $request->validate([
            'parcelle_id' => 'sometimes|required|exists:parcelles,id',
            'culture_id' => 'sometimes|required|exists:cultures,id',
            'nombre' => 'sometimes|required|integer|min:1',
        ]);

        $arbre->update($validated);
        
        return response()->json($arbre->load(['parcelle', 'culture']));
    }

    public function destroy(Arbre $arbre)
    {
        $arbre->delete();
        return response()->json(null, 204);
    }
}
