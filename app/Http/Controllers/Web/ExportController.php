<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OrganisationPaysanne;
use App\Models\Producteur;
use App\Models\Culture;
use App\Models\Parcelle;
use App\Exports\OrganisationsExport;
use App\Exports\ProducteursExport;
use App\Exports\CulturesExport;
use App\Exports\ParcellesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // ──────────────────────────────────────────────
    //  ORGANISATIONS PAYSANNES
    // ──────────────────────────────────────────────
    public function organisationsPdf()
    {
        $organisations = OrganisationPaysanne::with('zone', 'village', 'controleur')
            ->withCount('producteurs')
            ->orderBy('nom')
            ->get();

        $pdf = Pdf::loadView('exports.organisations-pdf', compact('organisations'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('organisations-paysannes-' . now()->format('Y-m-d') . '.pdf');
    }

    public function organisationsExcel()
    {
        $filename = 'organisations-paysannes-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new OrganisationsExport, $filename);
    }

    // ──────────────────────────────────────────────
    //  PRODUCTEURS
    // ──────────────────────────────────────────────
    public function producteursPdf()
    {
        $producteurs = Producteur::with('zone', 'village', 'organisation', 'controleur')
            ->withCount('parcelles')
            ->orderBy('nom')
            ->get();

        $pdf = Pdf::loadView('exports.producteurs-pdf', compact('producteurs'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('producteurs-' . now()->format('Y-m-d') . '.pdf');
    }

    public function producteursExcel()
    {
        $filename = 'producteurs-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ProducteursExport, $filename);
    }

    // ──────────────────────────────────────────────
    //  CULTURES
    // ──────────────────────────────────────────────
    public function culturesPdf()
    {
        $cultures = Culture::orderBy('nom')->get();

        $pdf = Pdf::loadView('exports.cultures-pdf', compact('cultures'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('cultures-' . now()->format('Y-m-d') . '.pdf');
    }

    public function culturesExcel()
    {
        $filename = 'cultures-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new CulturesExport, $filename);
    }

    // ──────────────────────────────────────────────
    //  PARCELLES
    // ──────────────────────────────────────────────
    public function parcellesPdf()
    {
        $parcelles = Parcelle::with('producteur', 'village', 'culture')
            ->orderBy('indice')
            ->get();

        $pdf = Pdf::loadView('exports.parcelles-pdf', compact('parcelles'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('parcelles-' . now()->format('Y-m-d') . '.pdf');
    }

    public function parcellesExcel()
    {
        $filename = 'parcelles-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new ParcellesExport, $filename);
    }
}
