<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;

class ParametreApiController extends Controller
{
    public function index()
    {
        return response()->json(Parametre::all());
    }
}
