<?php

namespace Database\Seeders;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    public function run(): void
    {
        $sophie = User::where('email', 'sophie@example.com')->first();
        $thomas = User::where('email', 'thomas@example.com')->first();

        Offre::create([
            'titre' => 'Développeur Laravel Senior',
            'description' => "Nous recherchons un développeur Laravel senior pour rejoindre notre équipe technique. Vous serez responsable du développement et de la maintenance de nos applications web, de la conception des architectures backend et de l'encadrement des développeurs juniors.",
            'competences_requises' => ['PHP', 'Laravel', 'MySQL', 'Git', 'Docker', 'REST API', 'CI/CD'],
            'niveau_experience' => 'senior',
            'user_id' => $sophie->id,
        ]);

        Offre::create([
            'titre' => 'Chef de Projet Digital',
            'description' => "En tant que Chef de Projet Digital, vous piloterez des projets web et mobile de la conception à la livraison. Vous serez l'interface entre les équipes techniques et les clients, et garantirez le respect des délais et du budget.",
            'competences_requises' => ['Gestion de projet', 'Agile/Scrum', 'Communication', 'Analyse fonctionnelle', 'JIRA', 'Connaissance du web'],
            'niveau_experience' => 'confirme',
            'user_id' => $sophie->id,
        ]);

        Offre::create([
            'titre' => 'Data Analyst Junior',
            'description' => "Nous cherchons un Data Analyst motivé pour rejoindre notre pôle data. Vous participerez à la collecte, au traitement et à l'analyse des données pour aider à la prise de décision stratégique.",
            'competences_requises' => ['SQL', 'Python', 'Excel', 'Visualisation de données', 'Statistiques'],
            'niveau_experience' => 'junior',
            'user_id' => $thomas->id,
        ]);
    }
}
