<?php

namespace App\Controllers;

use App\core\Backup;

class Export
{
    public function generate()
    {
        // Logique pour générer l'archive et la télécharger
        $backup = new Backup();
        $backup->generateArchive();
    }
    // Fonction pour exporter la base de données
    public function exportDatabase()
    {
        $dbname = 'votre_nom_de_bdd';
        $host = 'localhost';  // ou l'hôte de votre BDD
        $user = 'votre_utilisateur';
        $password = 'votre_mot_de_passe';
        $exportPath = 'exports/sql_dump.sql';  // Chemin où sera enregistré le dump SQL

        // Commande pour exporter la base de données PostgreSQL
        $command = "PGPASSWORD='{$password}' pg_dump -h {$host} -U {$user} -F c -b -v -f {$exportPath} {$dbname}";

        // Exécution de la commande shell
        exec($command, $output, $result);

        if ($result !== 0) {
            echo "Erreur lors de l'exportation de la base de données";
        } else {
            return $exportPath;
        }
    }

    // Fonction pour exporter tous les fichiers et la base de données dans une archive ZIP
    public function exportFullSolution()
    {
        $this->exportDatabase(); // Exporte la BDD

        // Chemin de destination pour le fichier ZIP
        $zipPath = 'exports/solution_export.zip';

        // Initialisation de l'archive ZIP
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            // Ajout des fichiers du projet
            $this->addFolderToZip('C:/Users/jerem/NextFrame', $zip);

            // Ajout du fichier SQL dump
            $zip->addFile('exports/sql_dump.sql', 'sql_dump.sql');

            // Fermeture de l'archive ZIP
            $zip->close();
            return $zipPath;
        } else {
            echo 'Erreur lors de la création du fichier ZIP';
        }
    }

    // Fonction récursive pour ajouter un dossier entier dans l'archive ZIP
    private function addFolderToZip($folder, &$zip, $parentFolder = '')
    {
        $handle = opendir($folder);
        while ($file = readdir($handle)) {
            if ($file !== '.' && $file !== '..') {
                $path = $folder . '/' . $file;
                if (is_dir($path)) {
                    $this->addFolderToZip($path, $zip, $parentFolder . $file . '/');
                } else {
                    $zip->addFile($path, $parentFolder . $file);
                }
            }
        }
        closedir($handle);
    }

    // Fonction pour télécharger le fichier ZIP
    public function downloadZip()
    {
        $zipPath = $this->exportFullSolution();

        if (file_exists($zipPath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);
            exit;
        } else {
            echo "Fichier ZIP non trouvé";
        }
    }
}
