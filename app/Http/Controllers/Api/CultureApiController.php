<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Culture;
use Illuminate\Http\Request;

class CultureApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Culture::query();
        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }
        return response()->json($query->get());
    }
}
