<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Controle extends Model
{
    protected $fillable = [
        'numero', 'parcelle_id', 'producteur_id', 'culture_id',
        'controleur_id', 'superficie_parcelle', 'superficie_bio', 'campagne'
    ];

    public function parcelle(): BelongsTo   { return $this->belongsTo(Parcelle::class); }
    public function producteur(): BelongsTo { return $this->belongsTo(Producteur::class); }
    public function culture(): BelongsTo    { return $this->belongsTo(Culture::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
}
