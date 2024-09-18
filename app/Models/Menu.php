<?php

namespace App\Models;

use App\Core\DB;

class Menu extends DB
{
    private ?int $id = null;
    protected string $label;
    protected string $url;
    protected ?int $id_page = null; // Lien vers la page
    protected ?int $parent_id = null; // Pour gérer les sous-menus
    protected int $position; // Ordre d'affichage
    protected ?string $created_at;
    protected ?string $updated_at;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getIdPage()
    {
        return $this->id_page;
    }

    public function setIdPage($id_page)
    {
        $this->id_page = $id_page;
    }

    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function save(): void
    {
        $data = [
            'label' => $this->getLabel(),
            'url' => $this->getUrl(),
            'id_page' => $this->getIdPage(),
            'parent_id' => $this->getParentId(),
            'position' => $this->getPosition(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $sql = "INSERT INTO menu (label, url, id_page, parent_id, position, created_at, updated_at) 
                VALUES (:label, :url, :id_page, :parent_id, :position, :created_at, :updated_at)";
        
        $this->exec($sql, $data);
        error_log("Menu entry inserted: " . json_encode($data));
    }


    public function getLastMenuItem(): ?array
    {
        $result = $this->exec("SELECT * FROM menu ORDER BY position DESC LIMIT 1");
        return $result ? $result[0] : null;
    }

    public function getAllWithPages(): array
    {
        $sql = "SELECT menu.*, pages.url as page_url, pages.title as page_title 
                FROM menu
                LEFT JOIN pages ON menu.id_page = pages.id
                ORDER BY menu.position";
        return $this->exec($sql);
    }
}
