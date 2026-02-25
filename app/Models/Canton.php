<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canton extends Model
{
    protected $fillable = ['region_id', 'prefecture_id', 'nom', 'zone'];

    public function region(): BelongsTo     { return $this->belongsTo(Region::class); }
    public function prefecture(): BelongsTo { return $this->belongsTo(Prefecture::class); }
    public function villages(): HasMany     { return $this->hasMany(Village::class); }
}
