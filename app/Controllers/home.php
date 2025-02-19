<?php

namespace App\Controllers;

use App\Core\DB;
use App\Models\Page;
use App\Controllers\Error;

class Home
{
    public function __construct()
    {
        define("BASE_DIR", __DIR__ . "/..");
    }

    // Affiche la page d'accueil
    public function index(): void
    {
        if ($_SERVER["REQUEST_URI"] === "/") {
            if (
                file_exists(__DIR__ . "/../config/config.php") &&
                !empty(file_get_contents(__DIR__ . "/../config/config.php"))
            ) {
                $Page = new Page();
                $homePage = $Page->getOneBy(["url" => "/home"]);

                if ($homePage) {
                    $pageData = $homePage[0];
                    include __DIR__ . "/../Views/front-office/page/page.php";
                } else {
                    include __DIR__ . "/../Views/front-office/main/home.php";
                }
            } else {
                include __DIR__ . "/../Views/front-office/main/home.php";
            }
        } else {
            $Error = new Error();
            $Error->error404();
        }
    }

    // Affiche la page de l'utilisateur
    public function mypage($uri = ''): bool
    {
        $Page = new Page();
        $Data = $Page->getAll();
        $pageData = null;
        foreach ($Data as $page) {
            if ($page['url'] === $uri) {
                $pageData = $page;
                break;
            }
        }
        if (!$pageData) {
            return false;
        } else {
            include __DIR__ . '/../Views/front-office/page/page.php';
            return true;
        }
    }
}
