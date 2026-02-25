<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['nom', 'mot_de_passe'];
    protected $hidden   = ['mot_de_passe'];

    protected static function booted(): void
    {
        static::creating(function (Zone $z) {
            if ($z->mot_de_passe && !str_starts_with($z->mot_de_passe, '$2y$')) {
                $z->mot_de_passe = bcrypt($z->mot_de_passe);
            }
        });
    }

    public function producteurs(): HasMany   { return $this->hasMany(Producteur::class); }
    public function organisations(): HasMany { return $this->hasMany(OrganisationPaysanne::class); }
    public function users(): HasMany         { return $this->hasMany(User::class); }
}
