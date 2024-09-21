<?php

namespace App\Core;

class Backup
{
    public function generateArchive()
    {
        // Création du dossier backup s'il n'existe pas
        $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/../backup/';
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // Chemins des fichiers à inclure dans l'archive
        $files = [
            $_SERVER['DOCUMENT_ROOT'] . '/../app',
            $_SERVER['DOCUMENT_ROOT'] . '/../resources',
            $_SERVER['DOCUMENT_ROOT'] . '/../public',
            // Ajoute d'autres chemins nécessaires
        ];

        // Chemin de l'archive ZIP
        $archiveFile = $backupDir . 'backup_' . date('Ymd_His') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($archiveFile, \ZipArchive::CREATE) === true) {
            foreach ($files as $file) {
                $this->addFilesToZip($zip, $file, basename($file));
            }

            // Exportation de la base de données PostgreSQL
            $this->exportDatabase();

            // Chemin du fichier SQL exporté
            $sqlBackupFile = $backupDir . 'database_' . date('Ymd_His') . '.sql';

            // Ajouter le fichier SQL dans l'archive ZIP
            if (file_exists($sqlBackupFile)) {
                $zip->addFile($sqlBackupFile, basename($sqlBackupFile));
            } else {
                error_log("Le fichier SQL n'a pas été trouvé : $sqlBackupFile");
            }

            $zip->close();
        } else {
            error_log("Impossible de créer l'archive ZIP.");
        }

        // Envoi du fichier zip au client pour téléchargement
        if (file_exists($archiveFile)) {
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . basename($archiveFile));
            header('Content-Length: ' . filesize($archiveFile));
            readfile($archiveFile);
        } else {
            error_log("Le fichier ZIP n'a pas été créé.");
        }
    }




    private function addFilesToZip($zip, $path, $internalPath)
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                $files = scandir($path);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $this->addFilesToZip($zip, $path . '/' . $file, $internalPath . '/' . $file);
                    }
                }
            } else {
                $zip->addFile($path, $internalPath);
            }
        } else {
            error_log("Le fichier ou répertoire suivant n'existe pas : $path");
        }
    }


    private function exportDatabase()
    {
        // Inclure le fichier Config.php pour avoir accès aux constantes
        include_once $_SERVER['DOCUMENT_ROOT'] . '/../app/config/config.php';

        // Utilisation des constantes pour la base de données
        $database = DB_NAME;
        $user = DB_USER;
        $password = DB_PASSWORD;
        $host = DB_HOST;
        $port = DB_PORT;
        $backupFile = $_SERVER['DOCUMENT_ROOT'] . '/../backup/database_' . date('Ymd_His') . '.sql';

        // Vérifier si le dossier backup existe et le créer sinon
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0777, true);
        }

        // Commande pour exporter la base de données PostgreSQL
        $command = "PGPASSWORD='$password' pg_dump -U $user -h $host -p $port $database > $backupFile";

        // Exécuter la commande et capturer les erreurs
        exec($command, $output, $result);

        // Vérifier si la commande a échoué
        if ($result !== 0) {
            error_log("Erreur lors de l'exportation de la base de données : " . implode("\n", $output));
        } else {
            error_log("Exportation de la base de données réussie : $backupFile");
        }
    }
}
