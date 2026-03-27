<?php

namespace App\Exports;

use App\Models\Culture;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CulturesExport implements
    FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Culture::orderBy('nom')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nom de la culture', 'Créé le'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nom,
            $row->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4A7C59']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
