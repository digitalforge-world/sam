<?php

namespace App\Models;

use App\Models\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = ['region_id', 'prefecture_id', 'canton_id', 'controleur_id', 'nom', 'zone'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope());
    }


    public function region(): BelongsTo     { return $this->belongsTo(Region::class); }
    public function prefecture(): BelongsTo { return $this->belongsTo(Prefecture::class); }
    public function canton(): BelongsTo     { return $this->belongsTo(Canton::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
    public function producteurs(): HasMany  { return $this->hasMany(Producteur::class); }
}
