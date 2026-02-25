<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Identification extends Model
{
    protected $fillable = [
        'numero', 'producteur_id', 'controleur_id',
        'superficie', 'statut', 'approbation', 'campagne'
    ];

    public function producteur(): BelongsTo { return $this->belongsTo(Producteur::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
}
