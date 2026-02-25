<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['nom'];

    public function prefectures(): HasMany { return $this->hasMany(Prefecture::class); }
    public function cantons(): HasMany     { return $this->hasMany(Canton::class); }
    public function villages(): HasMany    { return $this->hasMany(Village::class); }
}
