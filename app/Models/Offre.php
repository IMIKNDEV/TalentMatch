<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['titre', 'description', 'competences_requises', 'niveau_experience', 'user_id'])]
class Offre extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'competences_requises' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidats(): HasMany
    {
        return $this->hasMany(Candidat::class);
    }
}
