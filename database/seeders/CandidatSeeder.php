<?php

namespace Database\Seeders;

use App\Models\Candidat;
use App\Models\Offre;
use Illuminate\Database\Seeder;

class CandidatSeeder extends Seeder
{
    public function run(): void
    {
        $offreLaravel = Offre::where('titre', 'Développeur Laravel Senior')->first();
        $offreChefProjet = Offre::where('titre', 'Chef de Projet Digital')->first();
        $offreData = Offre::where('titre', 'Data Analyst Junior')->first();

        Candidat::create([
            'nom' => 'Jean Dupont',
            'cv_texte' => "Développeur PHP avec 6 ans d'expérience dont 4 ans sur Laravel.\n"
                ."Compétences : PHP, Laravel, MySQL, Docker, Git, CI/CD avec GitHub Actions, REST API, Vue.js.\n"
                ."Expériences : Lead developer sur une plateforme e-commerce Laravel (3 ans), Développeur backend PHP (3 ans).\n"
                ."Formation : Master en Informatique, Université Paris-Saclay.\n"
                .'Langues : Français (natif), Anglais (courant).',
            'offre_id' => $offreLaravel->id,
        ]);

        Candidat::create([
            'nom' => 'Marie Lefebvre',
            'cv_texte' => "Développeuse full-stack avec 5 ans d'expérience.\n"
                ."Compétences : Laravel, Symfony, MySQL, PostgreSQL, Docker, Kubernetes, AWS, React.\n"
                ."Expériences : Développeuse backend chez une startup fintech (3 ans), Développeuse web en agence (2 ans).\n"
                ."Formation : Diplôme d'ingénieur en Informatique, EPITA.\n"
                .'Langues : Français (natif), Anglais (technique).',
            'offre_id' => $offreLaravel->id,
        ]);

        Candidat::create([
            'nom' => 'Pierre Moreau',
            'cv_texte' => "Chef de projet avec 7 ans d'expérience dans le digital.\n"
                ."Compétences : Gestion de projet Agile, Scrum Master certifié, JIRA, Confluence, Analyse fonctionnelle, Management d'équipe.\n"
                ."Expériences : Chef de projet digital chez une agence web (4 ans), Product Owner (3 ans).\n"
                ."Certifications : Scrum Master (PSM I), ITIL Foundation.\n"
                .'Langues : Français (natif), Anglais (courant), Espagnol (intermédiaire).',
            'offre_id' => $offreChefProjet->id,
        ]);

        Candidat::create([
            'nom' => 'Camille Petit',
            'cv_texte' => "Cheffe de projet web avec 5 ans d'expérience.\n"
                ."Compétences : Pilotage de projet, Méthodes Agiles, Specification fonctionnelle, Recette, Animation d'ateliers, Notions de développement web.\n"
                ."Expériences : Cheffe de projet chez une startup e-commerce (3 ans), Assistante Chef de projet (2 ans).\n"
                ."Formation : Master Marketing Digital, HEC Paris.\n"
                .'Langues : Français (natif), Anglais (courant).',
            'offre_id' => $offreChefProjet->id,
        ]);

        Candidat::create([
            'nom' => 'Lucas Bernard',
            'cv_texte' => "Jeune diplômé en Data Science avec une première expérience en stage.\n"
                ."Compétences : Python (Pandas, NumPy, Scikit-learn), SQL, Tableau, Excel avancé, Power BI.\n"
                ."Expériences : Stage de 6 mois en tant que Data Analyst chez une entreprise de logistique.\n"
                ."Formation : Master Data Science, Université Paris-Dauphine.\n"
                ."Projets : Analyse exploratoire des ventes, Tableau de bord KPI, Modèle de prédiction des ventes.\n"
                .'Langues : Français (natif), Anglais (technique).',
            'offre_id' => $offreData->id,
        ]);

        Candidat::create([
            'nom' => 'Emma Robert',
            'cv_texte' => "Analyste data avec 2 ans d'expérience en alternance.\n"
                ."Compétences : SQL, Python, R, Excel, Power BI,数据分析.\n"
                ."Expériences : Alternance Data Analyst dans une banque (2 ans).\n"
                ."Formation : Licence en Mathématiques Appliquées, Master 1 Data Science en cours.\n"
                .'Langues : Français (natif), Anglais (intermédiaire).',
            'offre_id' => $offreData->id,
        ]);
    }
}
