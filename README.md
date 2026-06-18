# 🧠 TalentMatch — Assistant IA de Présélection RH

Application web Laravel de présélection automatisée de candidats, propulsée par un agent IA. Le **responsable RH** crée une offre d'emploi, soumet des CVs en texte, et l'IA analyse la correspondance entre chaque CV et l'offre — extraction des informations clés, matching score justifié, et recommandation structurée. Le RH peut ensuite « parler » à un assistant conversationnel qui garde le contexte de l'analyse pour approfondir, comparer des profils, ou préparer des entretiens.

---

## 🚀 Fonctionnalités Clés

- **Authentification Complète** : Inscription, connexion et déconnexion sécurisées (offres et analyses rattachées au RH).
- **Gestion des Offres (CRUD)** : Création d'offres avec titre, description, compétences requises et niveau d'expérience minimum.
- **Soumission de CVs** : Collage du texte d'un CV + nom du candidat, lancement de l'analyse contre une offre.
- **Analyse IA — Structured Output** : L'IA extrait les informations clés du candidat (compétences, années d'expérience, niveau d'études, langues) et génère un matching score (0-100) justifié, enregistré en base sous forme typée via un contrat JSON strict.
- **Recommandation Typée** : Chaque candidat reçoit une recommandation claire — `convoquer` / `attente` / `rejeter`.
- **Assistant Conversationnel avec Tools** : Le RH pose des questions en langage naturel sur un candidat analysé ; l'agent appelle des tools Laravel réels pour récupérer les données (jamais d'invention de réponse).
- **Mémoire de Conversation** : Le contexte des échanges est persisté — questions de suivi sans tout répéter.
- **Comparaison de Candidats (Bonus)** : L'assistant compare deux profils analysés sur la même offre et argumente une recommandation.
- **Classement Automatique (Bonus)** : Tri des candidats d'une offre par matching score décroissant.

---

### 🔐 Autorisation — Policies

| Action | Propriétaire (RH créateur) | Autre utilisateur RH |
|---|:---:|:---:|
| Voir / créer une offre | ✅ | ➖ (création toujours permise) |
| Modifier / supprimer une offre | ✅ | ❌ |
| Soumettre un CV sur une offre | ✅ | ❌ |
| Voir l'analyse d'un candidat | ✅ | ❌ |
| Discuter avec l'assistant sur un candidat | ✅ | ❌ |

---

## 📂 Structure du Projet

```bash
talentmatch/
├── compose.yaml                              # Services : laravel.test (app), mysql, phpmyadmin
├── AGENTS.md                                  # Contexte projet pour coding agents
├── openspec/  (ou .specify/)                  # Specs par feature, rédigées avant le code
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── OffreController.php           # CRUD offres
│   │   │   ├── CandidatController.php        # Soumission CV + lancement analyse
│   │   │   ├── AnalyseController.php         # Affichage analyse
│   │   │   ├── ChatController.php            # Endpoint assistant conversationnel
│   │   │   └── Auth/                         # Login, Register, Logout
│   │   ├── Requests/
│   │   │   ├── StoreOffreRequest.php
│   │   │   ├── UpdateOffreRequest.php
│   │   │   ├── StoreCandidatRequest.php
│   │   │   └── AskAssistantRequest.php
│   │   └── Resources/
│   │       └── AnalyseResource.php
│   ├── Models/
│   │   ├── Offre.php                         # hasMany candidats · cast competences_requises
│   │   ├── Candidat.php                      # belongsTo offre · hasOne analyse
│   │   ├── Analyse.php                       # casts JSON + enum · accessor recommandation_label
│   │   └── User.php                          # hasMany offres
│   ├── Casts/
│   │   └── RecommandationCast.php            # Eloquent Cast custom (si enum natif non utilisé)
│   ├── Jobs/
│   │   └── AnalyserCandidatJob.php           # Job — appelle l'IA en structured output
│   ├── AI/
│   │   ├── Schemas/
│   │   │   └── AnalyseCandidatSchema.php     # Schéma JSON imposé au SDK laravel/ai
│   │   ├── Tools/
│   │   │   ├── GetCandidateAnalysisTool.php
│   │   │   ├── GetJobRequirementsTool.php
│   │   │   └── CompareCandidatesTool.php
│   │   └── RecruitmentAgent.php              # Définition de l'agent conversationnel
│   └── Policies/
│       └── OffrePolicy.php
├── database/
│   ├── migrations/                           # users, offres, candidats, analyses
│   └── seeders/                              # Données de test
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── offres/
│   │   ├── index.blade.php                   # Liste des offres + nb candidats
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php                    # Détail offre + candidats + scores
│   ├── candidats/
│   │   ├── create.blade.php                  # Formulaire soumission CV
│   │   └── show.blade.php                    # Détail analyse + chat assistant
│   └── auth/
│       ├── login.blade.php
│       └── register.blade.php
├── routes/
│   └── web.php
└── README.md
```

---

## 🛠️ Installation & Setup

### Prérequis

- PHP >= 8.5
- Composer
- Docker Desktop (pour Laravel Sail)
- Git
- Une clé API Anthropic (`ANTHROPIC_API_KEY`)

### 1. Cloner le dépôt

```bash
git clone https://github.com/<votre-pseudo>/talentmatch.git
cd talentmatch
```

### 2. Copier le fichier d'environnement

```bash
cp .env.example .env
```

Vérifiez que les variables suivantes correspondent à votre configuration Docker :

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=talentmatch
DB_USERNAME=sail
DB_PASSWORD=password

ANTHROPIC_API_KEY=sk-ant-...
```

### 3. Installer les dépendances PHP

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Lancer l'environnement Docker (Sail)

```bash
./vendor/bin/sail up -d
```

> 💡 Ajoutez un alias pour plus de confort : `alias sail='./vendor/bin/sail'`

### 5. Générer la clé d'application

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Installer le SDK Laravel AI

```bash
./vendor/bin/sail composer require laravel/ai
./vendor/bin/sail artisan ai:install
```

### 7. Initialiser la base de données

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

Cette commande crée toutes les tables (y compris celles du SDK `laravel/ai` pour la mémoire de conversation) et insère des données de test.

### 8. Lancer le worker de queue (obligatoire pour l'analyse IA)

```bash
./vendor/bin/sail artisan queue:work
```

### 9. Accéder à l'application

| Service | URL |
|---|---|
| Application | [http://localhost](http://localhost) |
| phpMyAdmin | [http://localhost:8081](http://localhost:8081) |

### 🔑 Identifiants de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Agent RH | `rh@talentmatch.test` | `password` |

---

## 🗺️ Table des Routes

### Routes Web (Protégées par `auth`)

| Méthode | URI | Nom | Controller | Description |
|---|---|---|---|---|
| GET | `/register` | `register` | `Auth\RegisterController@show` | Formulaire inscription |
| POST | `/register` | — | `Auth\RegisterController@store` | Création compte |
| GET | `/login` | `login` | `Auth\LoginController@show` | Formulaire connexion |
| POST | `/login` | — | `Auth\LoginController@login` | Authentification |
| POST | `/logout` | `logout` | `Auth\LoginController@logout` | Déconnexion |
| GET | `/dashboard` | `offres.index` | `OffreController@index` | Liste des offres (US3) |
| GET | `/offres/create` | `offres.create` | `OffreController@create` | Formulaire création offre |
| POST | `/offres` | `offres.store` | `OffreController@store` | Création offre (US2) |
| GET | `/offres/{offre}` | `offres.show` | `OffreController@show` | Détail offre + candidats (US4) |
| GET | `/offres/{offre}/edit` | `offres.edit` | `OffreController@edit` | Formulaire modification |
| PUT | `/offres/{offre}` | `offres.update` | `OffreController@update` | Modification offre |
| DELETE | `/offres/{offre}` | `offres.destroy` | `OffreController@destroy` | Suppression offre |
| GET | `/offres/{offre}/candidats/create` | `candidats.create` | `CandidatController@create` | Formulaire soumission CV |
| POST | `/offres/{offre}/candidats` | `candidats.store` | `CandidatController@store` | Soumission CV + déclenche le job d'analyse (US5) |
| GET | `/candidats/{candidat}` | `candidats.show` | `CandidatController@show` | Détail candidat + analyse + chat (US7, US8) |
| POST | `/candidats/{candidat}/chat` | `chat.ask` | `ChatController@ask` | Question à l'assistant (US9, US10, US11) |
| GET | `/offres/{offre}/comparer` | `candidats.compare` | `CandidatController@compare` | Comparaison de 2 candidats (Bonus) |

**Exemple de payload `POST /candidats/{candidat}/chat` :**

```json
{
  "message": "Pourquoi ce candidat a-t-il eu un score de 72 ?"
}
```

**Exemple de réponse :**

```json
{
  "reply": "Le score de 72 s'explique par une bonne maîtrise de Laravel et PHP, mais une absence d'expérience en tests automatisés, ce qui était un critère explicite de l'offre.",
  "tools_called": ["getCandidateAnalysis", "getJobRequirements"]
}
```

---

## 🧩 Concepts Techniques Clés

### Couche 1 — Structured Output (Contrat JSON)

L'analyse du CV retourne un contrat JSON strict, imposé par le SDK `laravel/ai` :

```php
// app/AI/Schemas/AnalyseCandidatSchema.php
[
    'competences_extraites'      => 'array<string>',
    'annees_experience'          => 'integer',
    'niveau_etudes'               => 'string',
    'langues'                     => 'array<string>',
    'matching_score'              => 'integer (0-100)',
    'points_forts'                 => 'array<string>',
    'lacunes'                       => 'array<string>',
    'competences_manquantes'      => 'array<string>',
    'recommandation'               => 'enum: convoquer | attente | rejeter',
    'justification'                 => 'string',
]
```

### Eloquent Casts — Champs JSON typés

```php
// app/Models/Analyse.php
protected function casts(): array
{
    return [
        'competences_extraites'   => 'array',
        'langues'                  => 'array',
        'points_forts'              => 'array',
        'lacunes'                    => 'array',
        'competences_manquantes'   => 'array',
        'recommandation'             => RecommandationEnum::class,
    ];
}
```

### Jobs & Queues — Analyse asynchrone

Soumettre un CV ne bloque pas l'interface : l'appel à l'IA est dispatché dans un job.

```php
// CandidatController@store
$candidat = Candidat::create([...]);
AnalyserCandidatJob::dispatch($candidat);
return redirect()->route('candidats.show', $candidat)->with('info', 'Analyse en cours...');
```

### Couche 2 — Agent avec Tools et Mémoire

```php
// app/AI/RecruitmentAgent.php
class RecruitmentAgent
{
    protected array $tools = [
        GetCandidateAnalysisTool::class,
        GetJobRequirementsTool::class,
        CompareCandidatesTool::class,
    ];

    public function ask(Candidat $candidat, string $message): string
    {
        return Ai::agent($this->tools)
            ->withConversation($candidat->id) // mémoire persistée par le SDK
            ->ask($message);
    }
}
```

```php
// app/AI/Tools/GetCandidateAnalysisTool.php
class GetCandidateAnalysisTool extends Tool
{
    public function handle(int $candidatId): array
    {
        return Candidat::findOrFail($candidatId)->analyse->toArray();
    }
}
```

### Local Scope — Classement par score

```php
// app/Models/Analyse.php
public function scopeTriParScore(Builder $query): Builder
{
    return $query->orderByDesc('matching_score');
}
```

---

## 🧪 Vérifications Qualité

- **Autorisation (Policies)** : `OffrePolicy` gère l'accès aux offres et aux candidats associés. `$this->authorize()` appelé dans chaque controller. Zéro `abort(403)` manuel.
- **Validation (Form Requests)** : Toutes les entrées passent par une classe `FormRequest` dédiée. Zéro `$request->validate()` inline.
- **N+1 Query** : Eager Loading (`with()`) systématique sur `offre.candidats.analyse`. Vérifié avec Laravel Debugbar.
- **CSRF** : Directive `@csrf` présente sur tous les formulaires.
- **Sécurité des Routes** : Toutes les routes sensibles groupées sous le middleware `auth`.
- **Tools fiables** : L'agent n'invente jamais de données — toute réponse contextuelle passe par un tool Laravel qui interroge la base réelle.
- **Workflow AI-Assisted** : Specs rédigées avant chaque feature (OpenSpec / Spec Kit), `AGENTS.md` à la racine, Laravel Boost installé en dev dependency.

---

## 🧪 Tests

Pour exécuter la suite de tests Laravel dans cet environnement Docker, utilisez l'une des commandes suivantes :

- Exécuter tous les tests :
  ```bash
  ./vendor/bin/sail test
  ```
- Exécuter uniquement les tests liés à l'IA et aux analyses :
  ```bash
  ./vendor/bin/sail test --filter 'StructuredAnalysisTest|AgentToolsTest|ConversationMemoryTest'
  ```
- Exécuter un test spécifique :
  ```bash
  ./vendor/bin/sail test --filter StructuredAnalysisTest
  ```

Si vous préférez exécuter PHPUnit directement dans le conteneur :

```bash
docker compose exec laravel.test php artisan test
```

> ⚠️ Ne lancez pas `./vendor/bin/phpunit` directement depuis l'hôte si le conteneur Docker n'est pas actif, car la base de données `mysql` est configurée pour fonctionner dans le réseau Docker.

---

## 📄 Licence

Distribué sous licence Unlicensed