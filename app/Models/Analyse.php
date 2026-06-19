<?php

namespace App\Models;

use App\Enums\RecommandationEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'competences_extraites', 'annees_experience', 'niveau_etudes', 'langues',
    'matching_score', 'points_forts', 'lacunes', 'competences_manquantes',
    'recommandation', 'justification', 'candidat_id',
])]
class Analyse extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'competences_extraites' => 'array',
            'langues' => 'array',
            'points_forts' => 'array',
            'lacunes' => 'array',
            'competences_manquantes' => 'array',
            'recommandation' => RecommandationEnum::class,
        ];
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function getRecommandationLabelAttribute(): string
    {
        return $this->recommandation->label();
    }

    public function scopeTriParScore($query)
    {
        return $query->orderByDesc('matching_score');
    }
}
