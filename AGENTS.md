# TalentMatch — AGENTS.md

## Stack

- **Framework:** Laravel 13 / PHP 8.5
- **Database:** MySQL 8.0 via Docker Sail
- **AI SDK:** `laravel/ai` v0.8.1 (Anthropic Claude for structured output + agent)
- **Dev Tools:** `laravel/boost` v2.4.10 (MCP server), Laravel Debugbar, Laravel Sail
- **Spec Workflow:** OpenSpec (`.opencode/` skills + `/opsx:*` commands)
- **Node.js:** v24.15 (via nvm)
- **Containerization:** Docker (Sail) with MySQL 8.0 + phpMyAdmin

## Project — TalentMatch

Automated candidate pre-selection for HR. An HR manager creates job offers, submits CVs in plain text, and the AI analyzes the correspondence between each CV and the offer — extracting candidate info, generating a justified matching score (0-100), and producing a structured recommendation (`convoquer`/`attente`/`rejeter`). The HR agent can then chat with a conversational assistant that keeps context to deepen analysis, compare candidates, or prepare interview questions.

## Project Structure

```
app/
├── AI/
│   ├── Schemas/
│   │   └── AnalyseCandidatSchema.php       # Typed JSON schema passed to laravel/ai
│   ├── Agents/
│   │   └── RecruitmentAgent.php            # Conversational agent with 3 tools + memory
│   └── Tools/
│       ├── GetCandidateAnalysisTool.php    # Tool: retrieve Analyse from DB by candidat_id
│       ├── GetJobRequirementsTool.php      # Tool: retrieve Offre criteria by offre_id
│       └── CompareCandidatesTool.php       # Tool: compare two analyses side-by-side
├── Enums/
│   └── RecommandationEnum.php             # convoquer | attente | rejeter (backed string)
├── Http/
│   ├── Controllers/
│   │   ├── OffreController.php            # CRUD offres (resource controller)
│   │   ├── CandidatController.php         # Submit CV, show analysis detail
│   │   └── ChatController.php             # POST /candidats/{candidat}/chat
│   └── Requests/
│       ├── StoreOffreRequest.php
│       ├── UpdateOffreRequest.php
│       ├── StoreCandidatRequest.php
│       └── AskAssistantRequest.php
├── Jobs/
│   └── AnalyserCandidatJob.php            # Async: calls Anthropic, persists Analyse
├── Models/
│   ├── User.php                           # Default Laravel auth model
│   ├── Offre.php                          # $fillable, competences_requises → array cast
│   ├── Candidat.php                       # $fillable, belongsTo Offre, hasOne Analyse
│   └── Analyse.php                        # $fillable, array casts, RecommandationEnum cast
├── Policies/
│   └── OffrePolicy.php                    # viewAny, view, create, update, delete
└── Providers/
    └── AppServiceProvider.php

bootstrap/
├── providers.php
└── app.php

config/
├── ai.php                                 # ANTHROPIC_API_KEY provider config
├── app.php
├── database.php
├── boost.php
└── ... (other standard Laravel config files)

database/
└── migrations/
    ├── 0001_01_01_000000_create_users_table.php
    ├── 0001_01_01_000001_create_cache_table.php
    ├── 0001_01_01_000002_create_jobs_table.php
    ├── xxxx_xx_xx_create_offres_table.php
    ├── xxxx_xx_xx_create_candidats_table.php
    ├── xxxx_xx_xx_create_analyses_table.php
    └── 2026_06_19_114508_create_agent_conversations_table.php  # SDK conversation memory

openspec/
├── config.yaml                            # Project context for AI agents
├── specs/
│   └── analyse-ia-structured-output.md    # Spec: JSON contract, constraints, error handling
└── changes/                                # Active change proposals

.opencode/
├── skills/                                 # 5 OpenSpec skills
│   ├── openspec-propose/
│   ├── openspec-explore/
│   ├── openspec-apply-change/
│   ├── openspec-sync-specs/
│   └── openspec-archive-change/
└── commands/                               # 5 slash commands matching skills

.ai/
└── skills/
    ├── ai-sdk-development/                 # Skill from laravel/ai package
    └── laravel-best-practices/             # Skill from laravel/boost package

.mcp.json                                   # Boost MCP server config (./vendor/bin/sail artisan boost:mcp)
boost.json                                  # Boost configuration (guidelines, skills, mcp, sail enabled)
routes/web.php                              # All web routes
```

## Domain Models & Relationships

```
User 1──* Offre 1──* Candidat 1──1 Analyse
```

| Model | Table | Key Fields | Casts |
|-------|-------|-----------|-------|
| `User` | `users` | id, name, email, password, timestamps | — |
| `Offre` | `offres` | id, titre, description, competences_requises (json), niveau_experience, user_id (FK), timestamps | `competences_requises → array` |
| `Candidat` | `candidats` | id, nom, cv_texte (longText), offre_id (FK), timestamps | — |
| `Analyse` | `analyses` | id, competences_extraites (json), annees_experience, niveau_etudes, langues (json), matching_score (0-100), points_forts (json), lacunes (json), competences_manquantes (json), recommandation (enum), justification, candidat_id (FK unique), timestamps | JSON fields → `array`, `recommandation → RecommandationEnum` |

### Relationships

- **User** `hasMany` Offre
- **Offre** `belongsTo` User, `hasMany` Candidat
- **Candidat** `belongsTo` Offre, `hasOne` Analyse
- **Analyse** `belongsTo` Candidat

### RecommandationEnum

```php
enum RecommandationEnum: string
{
    case Convoquer = 'convoquer';
    case Attente   = 'attente';
    case Rejeter   = 'rejeter';

    public function label(): string
    {
        return match ($this) {
            self::Convoquer => 'À convoquer',
            self::Attente   => 'En attente',
            self::Rejeter   => 'À rejeter',
        };
    }
}
```

## JSON Contract — Structured Output (Layer 1)

Every AI analysis call **must enforce this exact schema** via `laravel/ai`'s typed schema system. No manual `json_decode()` or array casting in application code.

```json
{
  "competences_extraites": ["PHP", "Laravel", "MySQL"],
  "annees_experience": 5,
  "niveau_etudes": "Master en Informatique",
  "langues": ["Français (natif)", "Anglais (courant)"],
  "matching_score": 78,
  "points_forts": ["Maîtrise de Laravel", "Expérience en CI/CD"],
  "lacunes": ["Pas d'expérience en DevOps"],
  "competences_manquantes": ["Docker", "Kubernetes"],
  "recommandation": "convoquer",
  "justification": "Le candidat possède une solide expérience en Laravel correspondant au niveau senior requis."
}
```

### Constraints

| Field | Rule |
|-------|------|
| `matching_score` | Clamped to 0–100. If API returns outside range → default to 0, log warning |
| `recommandation` | Must be `convoquer`, `attente`, or `rejeter`. Any other value → default `attente` |
| `competences_extraites` | Empty CV → return `[]`, never fail |
| `justification` | Max 2000 characters; truncate if longer |

[Full spec →](openspec/specs/analyse-ia-structured-output.md)

## Required Tools — Layer 2 (Agent)

The conversational agent (`RecruitmentAgent`) has access to **3 real Laravel tools**. It must always call these for factual data instead of hallucinating.

| Tool | Signature | What it returns |
|------|-----------|----------------|
| `getCandidateAnalysis` | `getCandidateAnalysis(int $candidatId): array` | Full Analyse record from DB (all 10 fields) |
| `getJobRequirements` | `getJobRequirements(int $offreId): array` | titre, competences_requises, niveau_experience |
| `compareCandidates` | `compareCandidates(int $id1, int $id2): array` | Side-by-side comparison: scores, lacunes communes/différentes, competences_manquantes |

**Critical rule:** The agent must NEVER:
- Invent a matching_score
- Make up strengths or gaps
- Guess a recommendation
- Answer without calling the appropriate tool first

## Coding Conventions

### Validation
- All form validation goes through **Form Request** classes
- Zero inline `$request->validate()` calls in controllers
- Existing requests: `StoreOffreRequest`, `UpdateOffreRequest`, `StoreCandidatRequest`, `AskAssistantRequest`

### Authorization
- All access control via **Policies** using `$this->authorize()`
- `OffrePolicy`: viewAny (true), view (owner), create (true), update (owner), delete (owner)
- Zero `abort(403)` in controllers — ever

### Eloquent Casts
- No manual `json_decode()` or `json_encode()` anywhere in app code
- All JSON fields use typed casts: `$casts = ['field' => 'array']`
- `recommandation` uses a **custom cast** to `RecommandationEnum`

### Async AI
- CV analysis is **always** dispatched as `AnalyserCandidatJob` — never synchronous
- Job config: `$tries = 3`, exponential backoff (30s, 60s, 120s)
- Controller redirects immediately with flash message "Analyse en cours..."
- User can refresh the candidate detail page to see results once the job completes

### N+1 Prevention
- Always eager-load with `->with()` or `->load()`
- `OffreController@show`: `$offre->load(['candidats.analyse'])`
- Verify zero unexpected queries on every page via Debugbar SQL tab

### Conversation Memory
- Uses the SDK-provided `agent_conversations` table (already migrated)
- Conversation identifier: `user_id + candidat_id` for stable context
- Never implement custom session-based memory
- Follow-up questions must retain context from previous exchanges

## Key Artisan Commands

```bash
# Docker
./vendor/bin/sail up -d
./vendor/bin/sail down
./vendor/bin/sail ps

# Database
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan db:seed

# Queue (must be running for AI analysis)
./vendor/bin/sail artisan queue:work

# Development
./vendor/bin/sail artisan make:controller OffreController --resource
./vendor/bin/sail artisan make:model Offre -m
./vendor/bin/sail artisan make:job AnalyserCandidatJob
./vendor/bin/sail artisan make:request StoreOffreRequest
./vendor/bin/sail artisan make:policy OffrePolicy --model=Offre
./vendor/bin/sail artisan make:agent RecruitmentAgent
./vendor/bin/sail artisan make:tool GetCandidateAnalysisTool
./vendor/bin/sail npm run dev

# Boost
./vendor/bin/sail artisan boost:mcp                  # Start MCP server
./vendor/bin/sail artisan boost:list-skills            # List installed skills

# Testing
./vendor/bin/sail test
./vendor/bin/sail artisan config:clear

# Debugging
./vendor/bin/sail artisan pail                         # Tail logs
```

## OpenSpec Workflow

OpenSpec is initialized with `opencode` support. Use these slash commands:

| Command | When to use |
|---------|-------------|
| `/opsx:propose "my feature"` | Start a new feature: creates a change proposal |
| `/opsx:explore` | Browse existing specs and project context |
| `/opsx:apply` | Implement an approved proposal against the spec |
| `/opsx:sync` | Sync specs with current implementation |
| `/opsx:archive` | Finalize a completed change into source of truth |

**Workflow:**
1. Write the spec *before* writing any code
2. `/opsx:propose "description"` → generates `openspec/changes/`
3. Review the generated change files
4. `/opsx:apply` → agent reads the spec, implements code
5. `/opsx:archive` → proposal merged into `openspec/specs/` as source of truth

## Environment Variables

Key variables in `.env`:

```
APP_NAME=TalentMatch
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=talent_match
DB_USERNAME=sail
DB_PASSWORD=password

ANTHROPIC_API_KEY=sk-ant-...
QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_STORE=file
```

## Git Workflow

- **Feature branches:**
  - `feature/offres-crud` — Sprint 3 (Offres CRUD + Candidats)
  - `feature/analyse-ia` — Sprint 4 (Structured output, Jobs)
  - `feature/agent-conversationnel` — Sprint 5 (Agent, Tools, Memory)
- **Commit messages:** Explicit AI usage mention in every commit
  - ✅ `Add structured output schema for CV analysis (AI-assisted via Claude Code)`
  - ✅ `Implement RecruitmentAgent with 3 tools and conversation memory (AI-assisted)`
  - ✅ `Fix N+1 on offre dashboard with eager loading (manual fix)`
- **No direct commits to `main`** — all changes go through feature branches
- **Daily commits required** — at minimum one per day showing progress

## Boost MCP Server

Boost's local MCP server provides your coding agent with project schema, routes, and package versions.

```bash
# Start the MCP server (used by coding agents)
./vendor/bin/sail artisan boost:mcp
```

The MCP server is configured in `.mcp.json` at the project root.
