<?php

namespace App\Exports;

use App\Models\OrganisationPaysanne;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrganisationsExport implements
    FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return OrganisationPaysanne::with('zone', 'village', 'controleur')
            ->withCount('producteurs')
            ->orderBy('nom')
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'Nom', 'Zone', 'Village', 'Contrôleur', 'Nb Producteurs', 'Créé le'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nom,
            $row->zone->nom ?? '—',
            $row->village->nom ?? '—',
            $row->controleur ? $row->controleur->name . ' ' . $row->controleur->prenom : '—',
            $row->producteurs_count,
            $row->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2D6A4F']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
