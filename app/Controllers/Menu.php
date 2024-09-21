<?php

namespace App\Controllers;

use App\Models\Menu as MenuModel;

class Menu  
{
    public function save(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['label'], $_POST['url'], $_POST['position'])) {

                $menu = new MenuModel();
                if (isset($_POST['id']) && $_POST['id'] !== '') {
                    $menu->setId((int) $_POST['id']);
                    $menu->setUpdatedAt(date('Y-m-d H:i:s'));
                } else {
                    $menu->setCreatedAt(date('Y-m-d H:i:s'));
                }

                $menu->setLabel($_POST['label']);
                $menu->setUrl($_POST['url']);
                $menu->setPosition((int) $_POST['position']);
                $menu->setParentId($_POST['parent_id'] ?? null);

                $menu->save();

                header('Location: /dashboard/menu');
            }
        }
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id'])) {
                $menu = new MenuModel();
                $menu->delete($_POST['id']);
                header('Location: /dashboard/menu');
            }
        }
    }

    public function showAll(): array
    {
        $menuModel = new MenuModel();
        return $menuModel->getAll();
    }

    public function getMenuJson(): void
    {
        $menuModel = new MenuModel();
        $menus = $menuModel->getAllWithPages();
        echo json_encode($menus);
    }

}
