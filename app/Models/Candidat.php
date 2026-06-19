<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['nom', 'cv_texte', 'offre_id'])]
class Candidat extends Model
{
    use HasFactory;

    public function offre(): BelongsTo
    {
        return $this->belongsTo(Offre::class);
    }

    public function analyse(): HasOne
    {
        return $this->hasOne(Analyse::class);
    }
}
