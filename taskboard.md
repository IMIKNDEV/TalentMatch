# 🧠 TalentMatch — DevTrack Laravel SCRUM Board

**Stack:** Laravel 13 | PHP 8.5 | MySQL 8.0 | Laravel Sail | `laravel/ai` SDK

---

## 🐳 Docker Setup — Empty GitHub Repo (Start Here)

> Follow these steps **before anything else** on an empty cloned repo.

### Step 1 — Clone your repo and enter it

```bash
git clone https://github.com/<your-org>/talentmatch.git
cd talentmatch
```

### Step 2 — Create Laravel project in a temp folder, then move files

```bash
# Create Laravel app in a temp folder (outside the repo)
composer create-project laravel/laravel talentmatch-temp

# Copy everything into your cloned repo
cp -r talentmatch-temp/. .

# Remove the temp folder
rm -rf talentmatch-temp
```

### Step 3 — Install Laravel Sail

```bash
composer require laravel/sail --dev
php artisan sail:install
# When prompted, select: mysql
```

### Step 4 — Files to create / configure

**`.env`** — Edit the generated `.env`:

```env
APP_NAME=TalentMatch
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=talentmatch
DB_USERNAME=sail
DB_PASSWORD=password

ANTHROPIC_API_KEY=sk-ant-...
```

**`.gitignore`** — Verify these lines exist (add if missing):

```yaml
/vendor/
/node_modules/
.env
.env.backup
/storage/*.key
/public/hot
/public/storage
```

**`compose.yaml`** — Created automatically by Sail. Verify it contains these services:

```yaml
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.5
            dockerfile: Dockerfile
        ports:
            - '${APP_PORT:-80}:80'
        environment:
            - DB_HOST=mysql
        depends_on:
            - mysql
    mysql:
        image: 'mysql/mysql-server:8.0'
        ports:
            - '${FORWARD_DB_PORT:-3306}:3306'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
        volumes:
            - 'sail-mysql:/var/lib/mysql'
    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        ports:
            - '8081:80'
        environment:
            PMA_HOST: mysql
        depends_on:
            - mysql
volumes:
    sail-mysql:
        driver: local
```

> ⚠️ **phpMyAdmin is not added by Sail automatically** — add it manually to `compose.yaml` as shown above.
> ⚠️ Laravel 13 + PHP 8.5 may require building a custom Sail runtime if the official `8.5` image isn't published yet — check `vendor/laravel/sail/runtimes/` and fall back to `8.4` temporarily if needed.

### Step 5 — Start Docker and verify

```bash
./vendor/bin/sail up -d

# Add alias for convenience (add to ~/.bashrc or ~/.zshrc)
alias sail='./vendor/bin/sail'

# Verify the app is running
# → http://localhost should show the Laravel welcome page
```

### Step 6 — Generate app key

```bash
sail artisan key:generate
```

### Step 7 — Install Laravel Boost (AI-Assisted Workflow requirement)

```bash
sail composer require laravel/boost --dev
sail artisan boost:install
```

### Step 8 — Initial commit

```bash
git add .
git commit -m "Initial Laravel 13 + Sail setup with MySQL and phpMyAdmin"
git push origin main
```

### Step 9 — Create feature branches

```bash
git checkout -b feature/auth
git push origin feature/auth

git checkout main
git checkout -b feature/offres
git push origin feature/offres

git checkout main
git checkout -b feature/analyse-ia
git push origin feature/analyse-ia

git checkout main
git checkout -b feature/agent-conversationnel
git push origin feature/agent-conversationnel
```

---

## 📋 Legend

| Label | Meaning |
|-------|---------|
| `ARCH` | Architecture / Setup |
| `DOCKER` | Docker / Infrastructure |
| `AUTH` | Authentication |
| `OFFRE` | Job Offer Management |
| `CANDIDAT` | Candidate Submission |
| `AI-STRUCT` | Structured Output (Couche 1) |
| `AI-AGENT` | Agent / Tools / Memory (Couche 2) |
| `POLICY` | Policies & Authorization |
| `QA` | Code Quality / Security |
| `DEBUG` | Debugging Tools |
| `DOC` | Documentation / Livrables |
| `BONUS` | Bonus Feature |

---

## 🏃 Sprint 1 — Infrastructure & Setup

**Objectif:** Docker up, Laravel 13 initialized, migrations ready, `laravel/ai` SDK installed, Boost configured
**Durée:** Jour 1

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-01 | Initialize GitHub repo + branches | `ARCH` | High | 0.3h | **Action:**<br>- Create branches: `feature/auth`, `feature/offres`, `feature/analyse-ia`, `feature/agent-conversationnel`<br>- `.gitignore`: ignore `vendor/`, `.env`, `node_modules/`, `storage/*.key`<br>- `README.md`: push initial skeleton<br>- Invite teammate as collaborator on GitHub (if binôme) |
| [ ] | T-02 | Install Laravel 13 via Sail | `DOCKER` | High | 1h | **Action:**<br>- Follow the Docker Setup section above<br>- `composer create-project laravel/laravel talentmatch-temp` (must resolve to Laravel ^13.0)<br>- Copy files into cloned repo<br>- `composer require laravel/sail --dev`<br>- `php artisan sail:install` → choose **mysql**<br>- Add phpMyAdmin service manually to `compose.yaml`<br>- Confirm `composer.json` requires `"php": "^8.5"` |
| [ ] | T-03 | Start Docker + verify environment | `DOCKER` | High | 0.5h | **Action:**<br>- `./vendor/bin/sail up -d`<br>- Verify `http://localhost` → Laravel welcome page<br>- Verify `http://localhost:8081` → phpMyAdmin login<br>- Add alias: `alias sail='./vendor/bin/sail'` |
| [ ] | T-04 | Configure `.env` | `ARCH` | High | 0.3h | **Files to Edit:**<br>- `.env`: `APP_NAME=TalentMatch`, `DB_HOST=mysql`, `DB_DATABASE=talentmatch`, `DB_USERNAME=sail`, `DB_PASSWORD=password`<br>- `APP_URL=http://localhost`<br>- `ANTHROPIC_API_KEY=sk-ant-...`<br>- `QUEUE_CONNECTION=database` (ou `redis`)<br>- Run `sail artisan key:generate` if not done |
| [ ] | T-05 | Install `laravel/ai` SDK | `AI-STRUCT` | High | 1h | **Action:**<br>- `sail composer require laravel/ai`<br>- `sail artisan ai:install`<br>- Confirm conversation memory migrations are published (used in Sprint 4)<br>- `sail artisan migrate`<br>- Verify `config/ai.php` exists and `ANTHROPIC_API_KEY` is read correctly |
| [ ] | T-06 | Install Laravel Boost (dev dependency, MCP server) | `ARCH` | High | 0.5h | **Action:**<br>- `sail composer require laravel/boost --dev`<br>- `sail artisan boost:install`<br>- Verify the coding agent (Claude Code, Cursor, etc.) can read the schema/routes through Boost's local MCP server |
| [ ] | T-07 | Choose & init spec workflow (OpenSpec or Spec Kit) | `ARCH` | High | 0.5h | **Action:**<br>- Pick OpenSpec **or** Spec Kit (not both)<br>- Initialize the tool (`npx openspec init` or equivalent)<br>- Create `openspec/` (or `.specify/`) folder structure<br>- Write the first spec: `analyse-ia-structured-output.md` before touching any AI code |
| [ ] | T-08 | Create `AGENTS.md` at project root | `DOC` | High | 0.5h | **Files to Create:**<br>- `AGENTS.md` — stack, folder structure, conventions for any coding agent working on the repo<br>- Reference the JSON contract for structured output<br>- Reference the 3 required tools (`getCandidateAnalysis`, `getJobRequirements`, `compareCandidates`) |
| [ ] | T-09 | Migration — Table `users` (default) | `ARCH` | High | 0.2h | **Action:**<br>- Default Laravel `users` migration is already present<br>- Verify columns: `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `timestamps`<br>- No changes needed |
| [ ] | T-10 | Migration — Table `offres` | `ARCH` | High | 1h | **Files to Create:**<br>- `sail artisan make:migration create_offres_table`<br>- **Columns:** `id` (PK), `titre` (string, 255), `description` (text), `competences_requises` (json), `niveau_experience` (string), `user_id` (FK → users.id, onDelete cascade), `timestamps` |
| [ ] | T-11 | Migration — Table `candidats` | `ARCH` | High | 0.5h | **Files to Create:**<br>- `sail artisan make:migration create_candidats_table`<br>- **Columns:** `id` (PK), `nom` (string, 255), `cv_texte` (longText), `offre_id` (FK → offres.id, onDelete cascade), `timestamps` |
| [ ] | T-12 | Migration — Table `analyses` | `ARCH` | High | 1h | **Files to Create:**<br>- `sail artisan make:migration create_analyses_table`<br>- **Columns:** `id` (PK), `competences_extraites` (json), `annees_experience` (integer), `niveau_etudes` (string), `langues` (json), `matching_score` (unsignedTinyInteger, 0-100), `points_forts` (json), `lacunes` (json), `competences_manquantes` (json), `recommandation` (enum: `convoquer`,`attente`,`rejeter`), `justification` (text), `candidat_id` (FK → candidats.id, unique, onDelete cascade), `timestamps`<br>- `candidat_id` must be **unique** → enforces the 1:1 relationship |
| [ ] | T-13 | Model `Offre` + relationships + casts | `ARCH` | High | 1h | **Files to Create:**<br>- `sail artisan make:model Offre`<br>- `$fillable = ['titre', 'description', 'competences_requises', 'niveau_experience', 'user_id']`<br>- **Cast:** `competences_requises` → `array`<br>- Relationships:<br>&nbsp;&nbsp;- `user(): BelongsTo` → `User::class`<br>&nbsp;&nbsp;- `candidats(): HasMany` → `Candidat::class`<br>- **Bonus scope:** `scopeAvecMeilleurScore()` joining analyses |
| [ ] | T-14 | Model `Candidat` + relationships | `ARCH` | High | 0.5h | **Files to Create:**<br>- `sail artisan make:model Candidat`<br>- `$fillable = ['nom', 'cv_texte', 'offre_id']`<br>- Relationships:<br>&nbsp;&nbsp;- `offre(): BelongsTo` → `Offre::class`<br>&nbsp;&nbsp;- `analyse(): HasOne` → `Analyse::class` |
| [ ] | T-15 | Model `Analyse` + Eloquent Casts + accessor | `ARCH` | High | 1.5h | **Files to Create:**<br>- `sail artisan make:model Analyse`<br>- `$fillable` = all analysis fields<br>- **Casts (using `casts()` method, Laravel 13 style):**<br>&nbsp;&nbsp;`competences_extraites`, `langues`, `points_forts`, `lacunes`, `competences_manquantes` → `'array'`<br>&nbsp;&nbsp;`recommandation` → backed `enum RecommandationEnum::class`<br>- Relationship: `candidat(): BelongsTo` → `Candidat::class`<br>- **Accessor `getRecommandationLabelAttribute()`** → "À convoquer" / "En attente" / "À rejeter"<br>- **Scope `scopeTriParScore()`** → `orderByDesc('matching_score')` |
| [ ] | T-16 | Enum `RecommandationEnum` | `ARCH` | High | 0.3h | **Files to Create:**<br>- `app/Enums/RecommandationEnum.php`<br>- `enum RecommandationEnum: string { case Convoquer = 'convoquer'; case Attente = 'attente'; case Rejeter = 'rejeter'; }`<br>- Add `label()` method returning the French display string |
| [ ] | T-17 | Seeders | `ARCH` | High | 1.5h | **Files to Create:**<br>- `sail artisan make:seeder UserSeeder` → 1-2 RH users, password: `password`<br>- `sail artisan make:seeder OffreSeeder` → 2-3 offres avec compétences requises variées<br>- `sail artisan make:seeder CandidatSeeder` → 4-6 candidats répartis sur les offres, avec `cv_texte` réaliste<br>- `DatabaseSeeder.php`: call all three seeders in order<br>- `sail artisan migrate:fresh --seed` must pass ✅ |
| [ ] | T-18 | Install Laravel Debugbar | `DEBUG` | High | 0.5h | **Action:**<br>- `sail composer require barryvdh/laravel-debugbar --dev`<br>- Set `DEBUGBAR_ENABLED=true` in `.env` (dev only)<br>- Confirm the **SQL** tab is visible |
| [ ] | T-19 | Configure Queue worker | `ARCH` | High | 0.5h | **Action:**<br>- `QUEUE_CONNECTION=database` in `.env`<br>- `sail artisan queue:table` (si pas déjà fait par défaut sur Laravel 13)<br>- `sail artisan migrate`<br>- Verify `sail artisan queue:work` starts without error |

**Sprint 1 — Definition of Done:**

- [ ] `sail up -d` starts all services without error
- [ ] `http://localhost` shows the Laravel welcome page
- [ ] `http://localhost:8081` shows phpMyAdmin
- [ ] `sail artisan migrate:fresh --seed` runs cleanly with no errors
- [ ] `laravel/ai` SDK installed and `ANTHROPIC_API_KEY` confirmed working
- [ ] Laravel Boost installed and coding agent can read schema/routes through it
- [ ] First OpenSpec / Spec Kit spec written before any AI code
- [ ] `AGENTS.md` present at root
- [ ] Debugbar visible on every page
- [ ] All 3 domain models have `$fillable`, casts, and relationships defined

---

## 🏃 Sprint 2 — Authentication (US1)

**Objectif:** Registration, Login and Logout fully functional with main layout
**Durée:** Jour 2 matin
**Branch:** `feature/auth`

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-20 | Install Laravel Breeze (Blade) | `AUTH` | High | 0.5h | **Action:**<br>- `sail composer require laravel/breeze --dev`<br>- `sail artisan breeze:install blade`<br>- `sail npm install && sail npm run dev`<br>- `sail artisan migrate`<br>- Test `http://localhost/register` → register form must appear |
| [ ] | T-21 | `US1` — Register / Login / Logout | `AUTH` | High | 1.5h | **Files to Verify/Edit:**<br>- `resources/views/auth/register.blade.php`: fields `name`, `email`, `password`, `password_confirmation`, `@csrf`, `@error` messages<br>- `resources/views/auth/login.blade.php`: fields `email`, `password`, `@csrf`<br>- `RegisteredUserController@store`: validate → `User::create()` → redirect `/dashboard`<br>- `AuthenticatedSessionController@destroy`: `Auth::logout()` → redirect `/`<br>- **Routes:** `GET/POST /register`, `GET/POST /login`, `POST /logout` |
| [ ] | T-22 | Main layout `layouts/app.blade.php` | `AUTH` | High | 2h | **Files to Create/Edit:**<br>- `resources/views/layouts/app.blade.php`:<br>&nbsp;&nbsp;- `@vite(['resources/css/app.css', 'resources/js/app.js'])` in `<head>`<br>&nbsp;&nbsp;- Navbar with `@auth` → Dashboard + Logout button \| `@guest` → Login + Register<br>&nbsp;&nbsp;- Flash messages: `@if(session('success'))` and `@if(session('error'))`<br>&nbsp;&nbsp;- `@yield('content')` in main body |
| [ ] | T-23 | Protect all routes under `auth` middleware | `AUTH` | High | 0.5h | **Files to Edit:**<br>- `routes/web.php`: wrap all offre + candidat + chat routes in `Route::middleware('auth')->group(function () { ... })`<br>- Test: direct access to `/dashboard` without login → must redirect to `/login` |

**Sprint 2 — Definition of Done:**

- [ ] Registration creates a new user and redirects to `/dashboard`
- [ ] Login with seeded credentials works
- [ ] Logout redirects to `/` or `/login`
- [ ] Direct access to `/dashboard` without login → redirect `/login`
- [ ] `@auth`/`@guest` in layout shows correct nav links
- [ ] Flash messages display correctly

---

## 🏃 Sprint 3 — Offres CRUD (US2, US3, US4)

**Objectif:** Création, liste et détail des offres d'emploi
**Durée:** Jour 2 après-midi
**Branch:** `feature/offres`

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-24 | `OffrePolicy` — Authorization rules | `POLICY` | High | 1h | **Files to Create:**<br>- `sail artisan make:policy OffrePolicy --model=Offre`<br>- **Methods:**<br>&nbsp;&nbsp;- `viewAny(User $user)` → true<br>&nbsp;&nbsp;- `view(User $user, Offre $offre)` → `$offre->user_id === $user->id`<br>&nbsp;&nbsp;- `create(User $user)` → true<br>&nbsp;&nbsp;- `update(User $user, Offre $offre)` → owner only<br>&nbsp;&nbsp;- `delete(User $user, Offre $offre)` → owner only |
| [ ] | T-25 | `OffreController` — Scaffold | `OFFRE` | High | 0.5h | **Files to Create:**<br>- `sail artisan make:controller OffreController --resource`<br>- `$this->authorize()` in every relevant method<br>- `with()` on every query — zero N+1 |
| [ ] | T-26 | `US2` — Créer une offre | `OFFRE` | High | 1.5h | **Files to Create/Edit:**<br>- `OffreController@create` / `@store`:<br>&nbsp;&nbsp;- `$this->authorize('create', Offre::class)`<br>&nbsp;&nbsp;- Validate via `StoreOffreRequest`<br>&nbsp;&nbsp;- `Offre::create([...,'user_id' => auth()->id()])`<br>- `resources/views/offres/create.blade.php`: champs `titre`, `description`, `competences_requises` (input texte → converti en tableau, virgule-séparé), `niveau_experience` (select), `@csrf`<br>- **Routes:** `GET /offres/create` → `offres.create` · `POST /offres` → `offres.store` |
| [ ] | T-27 | `StoreOffreRequest` + `UpdateOffreRequest` | `ARCH` | High | 0.5h | **Files to Create:**<br>- `sail artisan make:request StoreOffreRequest`<br>&nbsp;&nbsp;- `rules()`: `titre\|required\|string\|max:255`, `description\|required\|string`, `competences_requises\|required\|string`, `niveau_experience\|required\|in:junior,confirme,senior`<br>- `sail artisan make:request UpdateOffreRequest` — same rules |
| [ ] | T-28 | `US3` — Liste de mes offres | `OFFRE` | High | 1.5h | **Files to Create/Edit:**<br>- `OffreController@index`:<br>&nbsp;&nbsp;- `Offre::where('user_id', auth()->id())->withCount('candidats')->latest()->get()`<br>- `resources/views/offres/index.blade.php`:<br>&nbsp;&nbsp;- Card par offre : titre, niveau d'expérience, nombre de candidats analysés (`$offre->candidats_count`)<br>&nbsp;&nbsp;- Lien vers `offres.show`<br>- **Route:** `GET /dashboard` → `offres.index` |
| [ ] | T-29 | `US4` — Détail d'une offre | `OFFRE` | High | 2h | **Files to Create/Edit:**<br>- `OffreController@show`:<br>&nbsp;&nbsp;- `$this->authorize('view', $offre)`<br>&nbsp;&nbsp;- `$offre->load(['candidats.analyse'])`<br>- `resources/views/offres/show.blade.php`:<br>&nbsp;&nbsp;- Critères de l'offre (compétences requises, niveau)<br>&nbsp;&nbsp;- Liste des candidats avec leur `matching_score` et badge de recommandation<br>&nbsp;&nbsp;- Bouton "Soumettre un CV"<br>&nbsp;&nbsp;- (Bonus) Tri décroissant par score via `->sortByDesc(fn($c) => $c->analyse?->matching_score)`<br>- **Route:** `GET /offres/{offre}` → `offres.show` |
| [ ] | T-30 | Edit / Delete offre | `OFFRE` | Medium | 1h | **Files to Create/Edit:**<br>- `OffreController@edit` / `@update` / `@destroy` — all gated by `$this->authorize('update'/'delete', $offre)`<br>- `resources/views/offres/edit.blade.php`: pre-filled form, `@method('PUT')`, `@csrf`<br>- **Routes:** `GET /offres/{offre}/edit`, `PUT /offres/{offre}`, `DELETE /offres/{offre}` |

**Sprint 3 — Definition of Done:**

- [ ] Création d'une offre attache automatiquement le `user_id` du RH connecté
- [ ] Dashboard liste uniquement les offres du RH connecté avec le compteur de candidats
- [ ] Détail offre affiche tous les candidats avec leur score
- [ ] Edit/Delete offre — 403 pour un RH non-propriétaire
- [ ] Zéro `abort(403)` — tout passe par `$this->authorize()`

---

## 🏃 Sprint 4 — Analyse IA Structurée (US5, US6, US7, US8)

**Objectif:** Soumission CV → Job → appel IA structured output → enregistrement typé → affichage
**Durée:** Jour 3
**Branch:** `feature/analyse-ia`

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-31 | Spec — Structured Output (OpenSpec/Spec Kit) | `AI-STRUCT` | High | 1h | **Action:**<br>- Rédiger la spec `analyse-candidat.md` **avant** d'écrire le code<br>- Décrire le contrat JSON exact, les cas limites (CV vide, score hors bornes), et le comportement attendu du job |
| [ ] | T-32 | Schéma JSON imposé au SDK `laravel/ai` | `AI-STRUCT` | High | 1.5h | **Files to Create:**<br>- `app/AI/Schemas/AnalyseCandidatSchema.php`<br>- Définir le schéma typé attendu par `laravel/ai` :<br>```php<br>competences_extraites: array<string><br>annees_experience: integer<br>niveau_etudes: string<br>langues: array<string><br>matching_score: integer (0-100)<br>points_forts: array<string><br>lacunes: array<string><br>competences_manquantes: array<string><br>recommandation: enum convoquer|attente|rejeter<br>justification: string<br>```<br>- Le SDK doit **imposer** ce schéma — pas de parsing JSON manuel côté app |
| [ ] | T-33 | `US5` — Soumettre un CV | `CANDIDAT` | High | 1.5h | **Files to Create/Edit:**<br>- `CandidatController@create` / `@store`:<br>&nbsp;&nbsp;- `$this->authorize('update', $offre)` (seul le propriétaire de l'offre peut soumettre)<br>&nbsp;&nbsp;- Validate via `StoreCandidatRequest` (`nom`, `cv_texte` required)<br>&nbsp;&nbsp;- `Candidat::create([...,'offre_id' => $offre->id])`<br>&nbsp;&nbsp;- `AnalyserCandidatJob::dispatch($candidat)`<br>&nbsp;&nbsp;- Redirect avec message "Analyse en cours..."<br>- `resources/views/candidats/create.blade.php`: champs `nom`, `cv_texte` (textarea), `@csrf`<br>- **Routes:** `GET /offres/{offre}/candidats/create` → `candidats.create` · `POST /offres/{offre}/candidats` → `candidats.store` |
| [ ] | T-34 | `US6` — Job d'analyse IA (structured output) | `AI-STRUCT` | High | 3h | **Files to Create:**<br>- `sail artisan make:job AnalyserCandidatJob`<br>- `handle()`:<br>&nbsp;&nbsp;1. Construire le prompt avec `$candidat->cv_texte` et les critères de `$candidat->offre`<br>&nbsp;&nbsp;2. Appeler `laravel/ai` avec le schéma `AnalyseCandidatSchema` imposé<br>&nbsp;&nbsp;3. Créer `Analyse::create([..., 'candidat_id' => $candidat->id])` à partir de la réponse typée<br>&nbsp;&nbsp;4. Gérer les erreurs (timeout IA, score hors bornes 0-100, recommandation invalide) avec retry/fail propre<br>- Configurer `$tries`, `$backoff` sur le job<br>- Tester avec `sail artisan queue:work` actif |
| [ ] | T-35 | `US7` — Voir l'analyse d'un candidat | `CANDIDAT` | High | 2h | **Files to Create/Edit:**<br>- `CandidatController@show`:<br>&nbsp;&nbsp;- `$this->authorize('view', $candidat->offre)`<br>&nbsp;&nbsp;- `$candidat->load('analyse', 'offre')`<br>&nbsp;&nbsp;- Si analyse pas encore terminée (job en cours) → afficher un état "en cours" et permettre un refresh<br>- `resources/views/candidats/show.blade.php`:<br>&nbsp;&nbsp;- Score affiché (`matching_score`/100)<br>&nbsp;&nbsp;- Sections : points forts, lacunes, compétences manquantes (listes)<br>&nbsp;&nbsp;- `justification` affichée en texte<br>- **Route:** `GET /candidats/{candidat}` → `candidats.show` |
| [ ] | T-36 | `US8` — Recommandation typée (badge) | `CANDIDAT` | High | 1h | **Files to Edit:**<br>- Utiliser `$analyse->recommandation` (enum cast) + `$analyse->recommandation_label` (accessor) dans la vue<br>- Badge coloré : vert "À convoquer", jaune "En attente", rouge "À rejeter"<br>- Même badge réutilisé dans `offres/show.blade.php` (liste des candidats) |
| [ ] | T-37 | Eloquent Casts — Vérification complète | `AI-STRUCT` | High | 0.5h | **Files to Audit:**<br>- `Analyse::casts()` retourne bien `array` pour tous les champs JSON et l'enum pour `recommandation`<br>- Confirmer qu'aucun `json_decode()` manuel n'est fait dans les controllers ou vues — tout passe par les casts |

**Sprint 4 — Definition of Done:**

- [ ] Soumission d'un CV déclenche bien un job en queue (pas d'appel IA synchrone bloquant)
- [ ] Le job écrit une `Analyse` strictement conforme au contrat JSON
- [ ] `matching_score` toujours compris entre 0 et 100
- [ ] `recommandation` toujours une des 3 valeurs enum valides
- [ ] Page de détail candidat affiche score, points forts, lacunes, compétences manquantes, justification
- [ ] Badge de recommandation visible et cohérent entre les vues
- [ ] Zéro parsing JSON manuel — tout passe par les Eloquent Casts

---

## 🏃 Sprint 5 — Agent Conversationnel (US9, US10, US11)

**Objectif:** Assistant avec tools réels + mémoire de conversation persistée
**Durée:** Jour 4
**Branch:** `feature/agent-conversationnel`

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-38 | Spec — Agent + Tools (OpenSpec/Spec Kit) | `AI-AGENT` | High | 1h | **Action:**<br>- Rédiger la spec `agent-conversationnel.md` avant le code<br>- Lister les 3 tools obligatoires, leurs signatures, et le comportement attendu en mémoire |
| [ ] | T-39 | Tool — `getCandidateAnalysis(int $candidatId)` | `AI-AGENT` | High | 1h | **Files to Create:**<br>- `app/AI/Tools/GetCandidateAnalysisTool.php`<br>- `handle(int $candidatId): array` → `Candidat::with('analyse')->findOrFail($candidatId)->analyse->toArray()`<br>- Gérer le cas où l'analyse n'existe pas encore (job en cours) |
| [ ] | T-40 | Tool — `getJobRequirements(int $offreId)` | `AI-AGENT` | High | 0.5h | **Files to Create:**<br>- `app/AI/Tools/GetJobRequirementsTool.php`<br>- `handle(int $offreId): array` → retourne titre, compétences requises, niveau d'expérience de l'offre |
| [ ] | T-41 | Tool — `compareCandidates(int $id1, int $id2)` | `AI-AGENT` | High | 1.5h | **Files to Create:**<br>- `app/AI/Tools/CompareCandidatesTool.php`<br>- `handle(int $id1, int $id2): array` → charge les deux analyses, retourne une structure comparative (scores, lacunes communes/différentes, compétences manquantes respectives)<br>- Vérifier que les deux candidats appartiennent bien à la même offre |
| [ ] | T-42 | Agent `RecruitmentAgent` — définition | `AI-AGENT` | High | 2h | **Files to Create:**<br>- `app/AI/RecruitmentAgent.php`<br>- Enregistrer les 3 tools<br>- Système de prompt clair : l'agent doit **toujours** utiliser les tools pour les données factuelles, jamais inventer un score ou une lacune<br>- Méthode `ask(Candidat $candidat, string $message): string` |
| [ ] | T-43 | `US10` — Mémoire de conversation (SDK) | `AI-AGENT` | High | 1.5h | **Action:**<br>- Utiliser les tables de conversation memory fournies par `laravel/ai` (publiées lors de `ai:install`, migrées en Sprint 1)<br>- Associer chaque conversation à un identifiant stable (ex: `candidat_id` ou `user_id + candidat_id`)<br>- Vérifier qu'une question de suivi («et pour l'entretien ?») garde le contexte sans répéter le candidat |
| [ ] | T-44 | `US9` + `US11` — Endpoint chat | `AI-AGENT` | High | 2h | **Files to Create/Edit:**<br>- `sail artisan make:controller ChatController`<br>- `ChatController@ask`:<br>&nbsp;&nbsp;- `$this->authorize('view', $candidat->offre)`<br>&nbsp;&nbsp;- Validate via `AskAssistantRequest` (`message\|required\|string\|max:1000`)<br>&nbsp;&nbsp;- `$reply = (new RecruitmentAgent)->ask($candidat, $validated['message'])`<br>&nbsp;&nbsp;- Retourne JSON `{ reply, tools_called? }`<br>- `resources/views/candidats/show.blade.php`: zone de chat (liste des échanges + input message), appel AJAX/fetch vers l'endpoint<br>- **Route:** `POST /candidats/{candidat}/chat` → `chat.ask` |
| [ ] | T-45 | `AskAssistantRequest` | `ARCH` | High | 0.3h | **Files to Create:**<br>- `sail artisan make:request AskAssistantRequest`<br>- `rules()`: `message\|required\|string\|max:1000` |
| [ ] | T-46 | Affichage historique des échanges | `AI-AGENT` | Medium | 1.5h | **Files to Edit:**<br>- Charger l'historique de conversation depuis les tables du SDK pour le candidat courant<br>- Afficher dans `candidats/show.blade.php` sous forme de bulles question/réponse<br>- Auto-scroll vers le bas à chaque nouvelle réponse |

**Sprint 5 — Definition of Done:**

- [ ] Une question sur un candidat renvoie une réponse contextuelle basée sur des données réelles (pas inventées)
- [ ] Les 3 tools sont bien appelés par l'agent selon la question posée
- [ ] Une question de suivi («pourquoi ce score ?» puis «et pour l'entretien ?») garde le contexte
- [ ] L'historique de conversation est persisté et rechargé correctement
- [ ] Zéro hallucination vérifiée manuellement sur au moins 5 questions tests

---

## 🏃 Sprint 6 — Bonus Features

**Objectif:** Comparaison de candidats + classement automatique
**Durée:** Jour 4 fin (si temps disponible)

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-47 | Bonus — Comparer deux candidats (UI) | `BONUS` | Low | 2h | **Files to Create/Edit:**<br>- `CandidatController@compare(Offre $offre, Request $request)`:<br>&nbsp;&nbsp;- `$this->authorize('view', $offre)`<br>&nbsp;&nbsp;- Récupère 2 `candidat_id` en query string<br>&nbsp;&nbsp;- Appelle directement le tool `CompareCandidatesTool` (ou passe par l'agent pour une recommandation argumentée)<br>- `resources/views/offres/compare.blade.php`: sélection de 2 candidats + affichage côte à côte<br>- **Route:** `GET /offres/{offre}/comparer` → `candidats.compare` |
| [ ] | T-48 | Bonus — Classement automatique par score | `BONUS` | Low | 1h | **Files to Edit:**<br>- `Analyse::scopeTriParScore()` déjà créé en Sprint 1 — l'utiliser dans `OffreController@show`<br>- `offres/show.blade.php`: tri visuel décroissant par `matching_score`, avec médaille/badge pour le top candidat |

---

## 🏃 Sprint 7 — QA, Debugging & Livrables

**Objectif:** Security audit, N+1 fix, README, commits check
**Durée:** Dernier jour

| Done | # | Task | Label | Priority | Time | Detailed Implementation & Files |
| :---: | :--- | :--- | :---: | :---: | :---: | :--- |
| [ ] | T-49 | Audit — All Form Requests in place | `QA` | High | 0.5h | **Files to Audit:**<br>- `OffreController@store`/`@update` → `StoreOffreRequest` / `UpdateOffreRequest` ✅<br>- `CandidatController@store` → `StoreCandidatRequest` ✅<br>- `ChatController@ask` → `AskAssistantRequest` ✅<br>- Zéro `$request->validate()` inline restant |
| [ ] | T-50 | Audit — `@csrf` on all forms | `QA` | High | 0.3h | **Forms to Audit:**<br>- `offres/create.blade.php`, `offres/edit.blade.php` → `@csrf` ✅<br>- `candidats/create.blade.php` → `@csrf` ✅<br>- Chat input (si formulaire HTML, sinon vérifier le header CSRF sur le fetch) ✅ |
| [ ] | T-51 | Audit — `$fillable` + casts sur tous les modèles | `QA` | High | 0.3h | **Files to Check:**<br>- `Offre::$fillable`, `Candidat::$fillable`, `Analyse::$fillable` corrects<br>- `Analyse::casts()` couvre bien tous les champs JSON + l'enum |
| [ ] | T-52 | Audit — Zéro `abort(403)` | `POLICY` | High | 0.3h | **Action:**<br>- `grep -r "abort(403)" app/Http/Controllers/` → doit retourner **0 résultats**<br>- Toute autorisation passe par `$this->authorize()` |
| [ ] | T-53 | Debugbar — Détecter et corriger les N+1 | `DEBUG` | High | 1h | **Action:**<br>- Ouvrir `/dashboard` → vérifier l'onglet SQL<br>- Ouvrir `/offres/{id}` → vérifier les requêtes sur `candidats.analyse` — corriger avec `with(['candidats.analyse'])` si besoin<br>- Confirmer un nombre de requêtes stable peu importe le nombre de candidats |
| [ ] | T-54 | Test du job en conditions réelles | `QA` | High | 1h | **Scénarios à vérifier:**<br>- Soumission CV → job en queue → analyse créée en moins de X secondes<br>- CV avec contenu minimal → l'IA gère gracieusement (pas de crash)<br>- Score toujours entre 0-100<br>- Recommandation toujours une valeur enum valide |
| [ ] | T-55 | Test du flux conversationnel complet | `QA` | High | 1h | **Scénarios à vérifier:**<br>- Question simple → réponse correcte basée sur les tools<br>- Question de suivi → contexte conservé<br>- Demande de comparaison → tool `compareCandidates` appelé<br>- Tentative d'accès au chat d'un candidat d'une offre appartenant à un autre RH → 403 |
| [ ] | T-56 | `README.md` complet | `DOC` | High | 0.5h | **Vérifier que README contient:**<br>- Description du projet<br>- Stack: Laravel 13, PHP 8.5, MySQL 8.0, Docker Sail, `laravel/ai`<br>- Instructions d'installation complètes (clone → env → sail up → ai:install → migrate:fresh --seed → queue:work)<br>- Credentials de test<br>- Table des routes<br>- Schéma ERD + diagramme de classes<br>- Section concepts clés (Structured Output, Eloquent Casts, Jobs & Queues, Tools, Conversation Memory) |
| [ ] | T-57 | `AGENTS.md` — vérification finale | `DOC` | High | 0.3h | **Action:**<br>- Confirmer que `AGENTS.md` reflète l'état réel du projet (pas un brouillon obsolète)<br>- Mentionne explicitement le contrat JSON et les 3 tools |
| [ ] | T-58 | Git audit — commits & branches | `DOC` | High | 0.3h | **Action:**<br>- `git log --oneline` → vérifier des commits quotidiens avec mention explicite de l'usage de l'IA<br>- Vérifier branches: `feature/auth`, `feature/offres`, `feature/analyse-ia`, `feature/agent-conversationnel`<br>- Zéro commit direct sur `main` (si binôme, PR + review)<br>- Exemples de messages corrects: `Add structured output schema for CV analysis (AI-assisted via Claude Code)`, `Implement RecruitmentAgent with 3 tools`, `Fix N+1 on offre dashboard with eager loading` |

**Sprint 7 — Definition of Done:**

- [ ] All forms have `@csrf` and use Form Request classes
- [ ] All models have `$fillable` and casts defined
- [ ] Zero `abort(403)` in controllers — all through `$this->authorize()`
- [ ] N+1 confirmed fixed via Debugbar
- [ ] Full candidate analysis flow tested end-to-end (submit → job → structured analysis)
- [ ] Full conversational flow tested end-to-end (ask → tools → memory)
- [ ] README complete with install instructions + credentials
- [ ] Daily commits with explicit AI usage mentions
- [ ] `AGENTS.md` accurate and up to date

---

## 📦 Final Deliverables Checklist

| Livrable | Critère | Statut |
|----------|---------|--------|
| GitHub Repo | Commits quotidiens avec mention claire de l'usage AI | ⬜ |
| GitHub Repo | Feature branches (`auth`, `offres`, `analyse-ia`, `agent-conversationnel`) | ⬜ |
| GitHub Repo | Zéro commit direct sur `main` (si binôme) | ⬜ |
| `AGENTS.md` | Présent à la racine, reflète l'état réel du projet | ⬜ |
| OpenSpec / Spec Kit | Spec rédigée avant chaque feature majeure | ⬜ |
| MCD | Entités, attributs, relations avec cardinalités (sans types ni FK) | ⬜ |
| MLD | Tables, types, PK, FK | ⬜ |
| README.md | Installation complète avec Sail/Docker + credentials de test | ⬜ |
| Migrations | Toutes les tables via migrations (zéro SQL manuel) | ⬜ |
| Seeders | Users, offres, candidats avec données réalistes | ⬜ |
| Structured Output | Contrat JSON respecté à 100%, casts Eloquent en place | ⬜ |
| Jobs & Queues | Analyse IA dispatchée en job, jamais synchrone | ⬜ |
| Agent + Tools | 3 tools fonctionnels, agent ne hallucine jamais | ⬜ |
| Conversation Memory | Mémoire persistée via tables SDK, contexte conservé | ⬜ |
| Debugbar | N+1 identifié et corrigé | ⬜ |

---

## 🏆 Performance Criteria

### Architecture Laravel (35%)

| Critère | Statut |
|---------|--------|
| `OffrePolicy` — `$this->authorize()` dans tous les controllers | ⬜ |
| Zéro `abort(403)` manuel dans le code | ⬜ |
| `StoreOffreRequest`, `UpdateOffreRequest`, `StoreCandidatRequest`, `AskAssistantRequest` utilisées | ⬜ |
| Eloquent Casts sur tous les champs JSON et l'enum `recommandation` | ⬜ |
| Jobs & Queues — analyse IA toujours asynchrone | ⬜ |
| Tools Laravel réels appelés par l'agent — zéro donnée inventée | ⬜ |
| Mémoire de conversation persistée et fonctionnelle | ⬜ |
| Zéro N+1 vérifié et confirmé avec Debugbar | ⬜ |

### Fonctionnalités (25%)

| Critère | Statut |
|---------|--------|
| CRUD offres complet | ⬜ |
| Soumission CV → analyse structurée complète et conforme au contrat JSON | ⬜ |
| Recommandation typée affichée clairement (convoquer/attente/rejeter) | ⬜ |
| Assistant conversationnel répond avec contexte et appelle les bons tools | ⬜ |

### Workflow AI-Assisted (20%)

| Critère | Statut |
|---------|--------|
| Laravel Boost installé et utilisé par le coding agent | ⬜ |
| Specs rédigées avant le code pour chaque feature majeure | ⬜ |
| `AGENTS.md` présent et à jour | ⬜ |
| Commits quotidiens avec mention claire de l'usage AI | ⬜ |

### Qualité & Process (20%)

| Critère | Statut |
|---------|--------|
| Feature branches utilisées de façon cohérente | ⬜ |
| README complet et installation reproductible | ⬜ |
| Tests passants (`sail test`) | ⬜ |
| Aucune donnée sensible committée (`.env` ignoré) | ⬜ |

---

*Dernière mise à jour : 18/06/2026*