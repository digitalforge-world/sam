<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prefecture extends Model
{
    protected $fillable = ['region_id', 'nom', 'code'];

    protected static function booted(): void
    {
        static::creating(function (Prefecture $p) {
            $p->code = strtoupper(substr($p->nom, 0, 2));
        });
    }

    public function region(): BelongsTo  { return $this->belongsTo(Region::class); }
    public function cantons(): HasMany   { return $this->hasMany(Canton::class); }
    public function villages(): HasMany  { return $this->hasMany(Village::class); }
}
