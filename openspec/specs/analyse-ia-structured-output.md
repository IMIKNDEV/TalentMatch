# Analyse IA — Structured Output Contract

## Description

When a recruiter submits a candidate's CV for a job offer, the system must dispatch an async job that calls the Anthropic API (via `laravel/ai` SDK) with a strict JSON schema. The response must be validated and persisted as an `Analyse` model.

## JSON Contract (imposed via `laravel/ai` Schema)

The SDK call must enforce this exact schema — no manual `json_decode()` or `array` casting in application code.

```php
[
    'competences_extraites' => 'array<string>',
    'annees_experience'     => 'integer',
    'niveau_etudes'         => 'string',
    'langues'               => 'array<string>',
    'matching_score'        => 'integer (0-100)',
    'points_forts'          => 'array<string>',
    'lacunes'               => 'array<string>',
    'competences_manquantes'=> 'array<string>',
    'recommandation'        => 'enum: convoquer|attente|rejeter',
    'justification'         => 'string',
]
```

## Constraints

| Field | Rule |
|-------|------|
| `matching_score` | Must be clamped to 0–100. If the API returns outside range, default to 0 and log a warning. |
| `recommandation` | Must be one of `convoquer`, `attente`, `rejeter`. Any other value → default to `attente`. |
| `competences_extraites` | If CV is empty or unparseable, return empty array `[]`, do not fail. |
| `justification` | Max 2000 characters; truncate if longer. |

## Flow

1. User submits CV via `CandidatController@store`
2. `AnalyserCandidatJob` is dispatched with `$candidat->id`
3. Job builds prompt from `$candidat->cv_texte` + `$candidat->offre` criteria
4. Job calls Anthropic via `laravel/ai` with the schema above
5. Response is validated, clamped, and persisted as `Analyse`
6. On failure (timeout, invalid response), job retries up to 3 times with exponential backoff

## Error Handling

- **API timeout**: Retry with backoff (30s, 60s, 120s)
- **Invalid JSON**: Log error, fail job after 3 retries
- **Score out of bounds**: Clamp to 0–100, log warning
- **Unknown recommandation**: Default to `attente`, log warning

## Files to Create

- `app/AI/Schemas/AnalyseCandidatSchema.php` — the typed schema passed to `laravel/ai`
- `app/Jobs/AnalyserCandidatJob.php` — the queued job
- `database/migrations/*_create_analyses_table.php` — already created (T-12)

## Acceptance Criteria

- [ ] `matching_score` is always 0–100 in the database
- [ ] `recommandation` is always one of the 3 enum values
- [ ] Empty CV produces an empty `competences_extraites` array (not null, not error)
- [ ] Job retries on failure instead of silently failing
- [ ] Zero manual `json_decode()` — all through Eloquent Casts
