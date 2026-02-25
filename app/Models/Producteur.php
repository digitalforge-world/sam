<?php

namespace App\Models;

use App\Models\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producteur extends Model
{
    protected $fillable = [
        'code', 'nom', 'prenom', 'zone_id', 'village_id',
        'organisation_paysanne_id', 'controleur_id', 'est_actif'
    ];

    protected $casts = ['est_actif' => 'boolean'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope());

        static::creating(function (Producteur $p) {
            $last = static::withoutGlobalScopes()->max('id') ?? 0;
            $p->code = 'PROD-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        });
    }

    public function zone(): BelongsTo          { return $this->belongsTo(Zone::class); }
    public function village(): BelongsTo       { return $this->belongsTo(Village::class); }
    public function organisation(): BelongsTo  { return $this->belongsTo(OrganisationPaysanne::class, 'organisation_paysanne_id'); }
    public function controleur(): BelongsTo    { return $this->belongsTo(User::class, 'controleur_id'); }
    public function parcelles(): HasMany       { return $this->hasMany(Parcelle::class); }
    public function identifications(): HasMany { return $this->hasMany(Identification::class); }

    public function scopeActif(Builder $q): Builder   { return $q->where('est_actif', true); }
    public function scopeDeZone(Builder $q, ?int $id): Builder
    {
        return $id ? $q->where('zone_id', $id) : $q;
    }
}
