<?php

namespace App\Models;

use App\Models\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationPaysanne extends Model
{
    protected $fillable = ['nom', 'zone_id', 'village_id', 'controleur_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope());
    }


    public function zone(): BelongsTo       { return $this->belongsTo(Zone::class); }
    public function village(): BelongsTo    { return $this->belongsTo(Village::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
    public function producteurs(): HasMany  { return $this->hasMany(Producteur::class); }
}
