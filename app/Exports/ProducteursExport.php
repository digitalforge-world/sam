<?php

namespace App\Exports;

use App\Models\Producteur;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProducteursExport implements
    FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Producteur::with('zone', 'village', 'organisation', 'controleur')
            ->withCount('parcelles')
            ->orderBy('nom')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code', 'Nom', 'Prénom', 'Sexe', 'Téléphone',
            'Zone', 'Village', 'Organisation', 'Type Carte',
            'Statut', 'Année Adhésion', 'Nb Parcelles', 'Actif', 'Créé le',
        ];
    }

    public function map($row): array
    {
        return [
            $row->code,
            $row->nom,
            $row->prenom,
            $row->sexe ?? '—',
            $row->telephone ?? '—',
            $row->zone->nom ?? '—',
            $row->village->nom ?? '—',
            $row->organisation->nom ?? '—',
            $row->type_carte ?? '—',
            $row->statut ?? '—',
            $row->annee_adhesion ?? '—',
            $row->parcelles_count,
            $row->est_actif ? 'Oui' : 'Non',
            $row->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1A1A2E']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
