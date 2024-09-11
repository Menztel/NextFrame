<?php

namespace App\Controllers;

use App\Models\User as UserModel;
use PHPMailer\PHPMailer\PHPMailer;


class User
{
    // Affiche la liste des utilisateurs
    public function showAll(): array
    {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION)) {
            session_start();
        }
        $userModel = new userModel();
        return $userModel->getAll();
    }

    // Affiche le profil de l'utilisateur
    public function editUser(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

            session_start();

            $user = new UserModel();
            // Récupère les informations de l'utilisateur connecté
            $userdata = $user->getOneBy(["email" => $_SESSION['user']['email']]);
            if (empty($userdata)) {
                $_SESSION['error_message'] = "Utilisateur introuvable";
                header('Location: /dashboard/user');
            } else {
                // Met à jour les informations de l'utilisateur
                $user->populate($userdata);
                // Vérifie si le login est renseigné sinon on met à jour unqieuement l'email
                if (!empty($login)) {
                    $user->setLogin($login);
                } else {
                    $user->setEmail($email);
                }
                $user->setUpdated_at(date('Y-m-d H:i:s'));
                $user->save();
                $_SESSION['success_message'] = "Vos informations ont été mises à jour avec succès";
            }
            header('Location: /dashboard/user');
        }
    }

    // Supprime un utilisateur de manière temporaire (soft delete)
    public function softDelete(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = filter_input(INPUT_POST, "id-user", FILTER_SANITIZE_NUMBER_INT);

            session_start();

            // Récupère les informations de l'utilisateur à supprimer
            $user = new UserModel();
            $userdata = $user->getOneBy(["id" => $id]);
            if (empty($userdata)) {
                $_SESSION['error_message'] = "Utilisateur introuvable";
            } else {
                // Vérifie si l'utilisateur connecté est un superadmin ou un admin
                if ($userdata[0]['role'] == 'superadmin' || $userdata[0]['role'] == 'admin') {
                    $_SESSION['error_message'] = "Vous ne pouvez pas supprimer un " . $userdata[0]['role'];
                } else {
                    // Supprime l'utilisateur de manière temporaire
                    $user->populate($userdata);
                    $user->setDeleted_at(date('Y-m-d H:i:s', strtotime("+30 days"))); // 30 jours
                    $user->setUpdated_at(date('Y-m-d H:i:s'));
                    $user->save();
                    $_SESSION['success_message'] = "L'utilisateur sera supprimé lors de la prochaine purge de la base de données";
                }
                header('Location: /dashboard/list-users');
            }
        }
    }

    // Supprime un utilisateur de manière définitive (hard delete)
    public function hardDelete(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = filter_input(INPUT_POST, "id-user", FILTER_SANITIZE_NUMBER_INT);

            session_start();

            // Récupère les informations de l'utilisateur à supprimer
            $user = new UserModel();
            $userdata = $user->getOneBy(["id" => $id]);
            if (empty($userdata)) {
                $_SESSION['error_message'] = "Utilisateur introuvable";
            } else {
                // Vérifie si l'utilisateur connecté est un superadmin ou un admin
                if ($userdata[0]['role'] == 'admin' || $userdata[0]['role'] == 'superadmin') {
                    $_SESSION['error_message'] = "Vous ne pouvez pas supprimer un ". $userdata[0]['role'];
                } else {
                    //envoyer un mail de confirmation de suppression à l'utilisateur
                    $mailConfig = include __DIR__ . "/../config/MailConfig.php";
                    $url = "http://localhost/dashboard/delete-user?id=" . $id; // à changer pour la mise en production
                    $email = $userdata[0]['email'];

                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $mailConfig['host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $mailConfig['username'];
                    $mail->Password = $mailConfig['password'];
                    $mail->SMTPSecure = $mailConfig['encryption'];
                    $mail->Port = $mailConfig['port'];

                    $mail->setFrom($mailConfig['from']['address'], $mailConfig['from']['name']);
                    $mail->addAddress($email);
                    $mail->Subject = "Confirmation de suppression de votre compte";
                    $mail->isHTML(true);
                    $mail->Body = "Votre sera supprimé si vous le confirmez en cliquant sur le lien suivant : <a href='" . $url . "'>Supprimer</a>";
                    $mail->send();

                    // Vérifie si le mail a été envoyé
                    if ($mail->ErrorInfo) {
                        $_SESSION['error_message'] = "Une erreur s'est produite lors de l'envoi du mail de confirmation";
                    } else {
                        $_SESSION['success_message'] = "L'utilisateur sera supprimé après confirmation de sa part";
                    }
                }
            }
            header('Location: /dashboard/list-users');
        }
    }
    
    // Supprime un utilisateur aprés confirmation de sa part
    public function delete(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

            if (!isset($_SESSION)) {
                session_start();
            }

            // Récupère les informations de l'utilisateur à supprimer
            $user = new UserModel();
            $userdata = $user->getOneBy(["id" => $id]);
            // Vérifie si l'utilisateur existe
            if (empty($userdata)) {
                $_SESSION['error_message'] = "Utilisateur introuvable";
            } else {
                // Supprime l'utilisateur de manière définitive
                $user->delete($id);
                $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès";
            }

            header('Location: /home?');
        }
    }

    // Met à jour le rôle d'un utilisateur
    public function updateRole()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = filter_input(INPUT_POST, "id-user", FILTER_SANITIZE_NUMBER_INT);
            $role = filter_input(INPUT_POST, "role", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            session_start();

            // Récupère les informations de l'utilisateur
            $user = new UserModel();
            $userdata = $user->getOneBy(["id" => $id]);

            if (empty($userdata)) {
                $_SESSION['error_message'] = "Utilisateur introuvable";
            } else {
                // Vérifie si l'utilisateur connecté est un superadmin
                if ($userdata[0]['role'] == 'superadmin') {
                    $_SESSION['error_message'] = "Vous ne pouvez pas changer le rôle d'un superadmin";
                } else {
                    // Met à jour le rôle de l'utilisateur
                    $user->populate($userdata);
                    $user->setRole($role);
                    $user->setUpdated_at(date('Y-m-d H:i:s'));
                    $user->save();
                    $_SESSION['success_message'] = "Le rôle de l'utilisateur a été mis à jour avec succès";
                }
            }
            header('Location: /dashboard/list-users');
        }
    }
}