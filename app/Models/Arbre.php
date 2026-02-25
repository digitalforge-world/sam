<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arbre extends Model
{
    protected $fillable = ['parcelle_id', 'culture_id', 'nombre'];

    public function parcelle(): BelongsTo { return $this->belongsTo(Parcelle::class); }
    public function culture(): BelongsTo  { return $this->belongsTo(Culture::class); }
}
