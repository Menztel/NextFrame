<?php

namespace App\Controllers;

use App\Models\Page;
use App\Controllers\Error;
use App\Models\Menu;

class PageBuilder
{

    // Affiche le back-office de création de page
    public function pageList()
    {
        $page = new Page();
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['role'] == 'admin' || $_SESSION['user']['role'] == 'superadmin') {
                // Mise à jour du sitemap
                $this->updateSiteMap();
                // Récupère toutes les pages de l'utilisateur connecté
                return $page->getAllBy(['id_creator' => $_SESSION['user']['id']]);
            }
        } else {
            $error = new Error();
            $error->error403();
        }
    }

    // Enregistre une page en base de données
    public function savePage(): void
    {
        session_start();

        $url = $_POST["url"] ?? '';
        $title = $_POST["title"] ?? '';
        $html = $_POST["html"] ?? '';
        $css = $_POST["css"] ?? '';
        $meta_description = $_POST["meta_description"] ?? '';

        if (!empty($url) && !empty($title) && !empty($html) && !empty($css) && !empty($meta_description)) {
            $Page = new Page();

            // Si l'id est renseigné, on met à jour la page
            if (!empty($_POST["id"])) {
                $id = $_POST["id"];
                $Page->setId($id);
                $Page->setUpdatedAt(date('Y-m-d H:i:s'));
            }

            $Page->setUrl('/' . htmlspecialchars($url));
            $Page->setTitle(htmlspecialchars($title));
            $Page->setHtml($html);
            $Page->setCss($css);
            $Page->setIdCreator($_SESSION['user']['id']);
            $Page->setMetaDescription($meta_description);
            $Page->save();

            $pageId = $Page->getId();
            error_log("Page ID after save: " . $pageId);

            if ($pageId) {
                $this->addToMenu($pageId, $title, $url);
                header('Content-Type: application/json');
                echo json_encode(["success" => true, "message" => "Page saved successfully"]);
            } else {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Error saving page, no ID found"]);
            }
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
        }
    }


    private function addToMenu(int $pageId, string $title, string $url): void
    {
        error_log("addToMenu - ID Page: $pageId, Titre: $title, URL: $url");

        $menu = new Menu();
        
        $existingMenu = $menu->getOneBy(['id_page' => $pageId]);

        if ($existingMenu) {
            
            $menu->setId($existingMenu['id']);
            $menu->setLabel($title);  
            $menu->setUrl($url);      
            $menu->setIdPage($pageId);
            $menu->setUpdatedAt(date('Y-m-d H:i:s')); 
        } else {
            
            $menu->setLabel($title);  
            $menu->setUrl($url);      
            $menu->setIdPage($pageId);
            $menu->setPosition($this->getNextMenuPosition());
        }

        error_log("Enregistrement du menu - Label: {$menu->getLabel()}, URL: {$menu->getUrl()}");
        $menu->save();
        error_log("Menu enregistré en base");
    }




    private function getNextMenuPosition(): int
    {
        $menuModel = new Menu();
        $lastMenuItem = $menuModel->getLastMenuItem();
        $position = $lastMenuItem ? $lastMenuItem['position'] + 1 : 1; 

        error_log("Prochaine position du menu: $position");
        return $position;
    }




    // Permet de supprimer une page en fonction de son id
    public function deletePage(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["id-page"])) {
                $id = $_POST["id-page"];
                
                // Supprimer d'abord les entrées du menu qui se réfèrent à cette page
                $menu = new Menu();
                $menu->deleteAllBy(['id_page' => $id]);

                // Supprime la page
                $Page = new Page();
                $Page->delete($id);

                if (!isset($_SESSION)) {
                    session_start();
                }

                // Vérifie si la page a bien été supprimée et affiche un message en conséquence
                if (!empty($Page->getOneBy(["id" => $id]))) {
                    $_SESSION['error_message'] = "La page n'a pas été supprimée";
                } else {
                    $_SESSION['success_message'] = "La page a été supprimée avec succès";
                }
            }
            header('Location: /dashboard/page-builder');
        }
    }


    // Met à jour le sitemap automatiquement
    private function updateSiteMap() : void
    {
        // Récupère toutes les pages
        $page = new Page();
        $pages = $page->getAll();
        // Crée le sitemap
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($pages as $page) {
            $priority = 0.5;
            if ($page['url'] === '/' || $page['url'] === '/home') {
                $priority = 1.0;
            } elseif (strpos($page['url'], '/Article/') === 0) {
                $priority = 0.7;
            }
            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '<loc>' . $_SERVER['HTTP_HOST'] . $page['url'] . '</loc>' . PHP_EOL;
            $sitemap .= '<lastmod>' . $page['updated_at'] . '</lastmod>' . PHP_EOL;
            $sitemap .= '<changefreq>monthly</changefreq>' . PHP_EOL;
            $sitemap .= '<priority>' . $priority . '</priority>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        $sitemap .= '</urlset>';

        // Enregistre le sitemap
        file_put_contents('../public/sitemap.xml', $sitemap);
    }
}