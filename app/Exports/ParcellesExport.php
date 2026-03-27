<?php

namespace App\Exports;

use App\Models\Parcelle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParcellesExport implements
    FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Parcelle::with('producteur', 'village', 'culture')
            ->orderBy('indice')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Indice', 'Producteur', 'Village', 'Culture',
            'Superficie (ha)', 'Superficie BIO (ha)', 'BIO',
            'Approbation', 'Créé le',
        ];
    }

    public function map($row): array
    {
        return [
            $row->indice,
            $row->producteur ? $row->producteur->nom . ' ' . $row->producteur->prenom : '—',
            $row->village->nom ?? '—',
            $row->culture->nom ?? '—',
            $row->superficie ? number_format($row->superficie, 2) : '—',
            $row->superficie_bio ? number_format($row->superficie_bio, 2) : '—',
            $row->bio ? 'Oui' : 'Non',
            $row->approbation_production ?? '—',
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
