<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcelle extends Model
{
    protected $fillable = [
        'producteur_id', 'village_id', 'culture_id', 'indice',
        'superficie', 'superficie_bio', 'rendement_bio', 'volume_production',
        'niveau_pente', 'type_culture', 'type_employes', 'approbation_production',
        'bio', 'a_cours_eau', 'maisons_proximite', 'transformation_ferme',
        'centre', 'contour',
    ];

    protected $casts = [
        'bio'                  => 'boolean',
        'a_cours_eau'          => 'boolean',
        'maisons_proximite'    => 'boolean',
        'transformation_ferme' => 'boolean',
        'centre'               => 'array',
        'contour'              => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Parcelle $p) {
            $max = static::where('producteur_id', $p->producteur_id)->max('indice') ?? 0;
            $p->indice = $max + 1;
        });
    }

    public function getCouleurCarteAttribute(): string
    {
        return match ($this->approbation_production) {
            'BIO'          => '#22c55e',
            'OK'           => '#eab308',
            'DECLASSIFIED' => '#ef4444',
            default        => '#94a3b8',
        };
    }

    public function producteur(): BelongsTo { return $this->belongsTo(Producteur::class); }
    public function village(): BelongsTo    { return $this->belongsTo(Village::class); }
    public function culture(): BelongsTo    { return $this->belongsTo(Culture::class); }
    public function arbres(): HasMany       { return $this->hasMany(Arbre::class); }
    public function controles(): HasMany    { return $this->hasMany(Controle::class); }
}
