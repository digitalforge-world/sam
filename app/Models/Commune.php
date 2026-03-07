<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commune extends Model
{
    protected $fillable = ['region_id', 'prefecture_id', 'nom'];

    public function region(): BelongsTo     { return $this->belongsTo(Region::class); }
    public function prefecture(): BelongsTo { return $this->belongsTo(Prefecture::class); }
}
