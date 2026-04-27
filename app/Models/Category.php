<?php

namespace App\Models;

use App\Core\AbstractModel;

class Category extends AbstractModel
{
    protected string $table = "categories";
    protected string $primaryKey = "id";

    protected array $fillable = [
        "name",
        "description"
    ];

    protected array $required = [
        "name" => "O campo NOME é obrigatório.",
        "description" => "O campo DESCRIPTION é obrigatório."
    ];

    protected bool $timestamps = true;

    public function getId(): ?int
    {
        return $this->attributes["id"];
    }

    public function setName(string $name): void
    {
        $name = trim(strip_tags($name));

        if (strlen($name) < 5) {
            throw new \InvalidArgumentException("A Categoria deve ter pelo menos 5 caracteries.");
        }

        $this->attributes["name"] = $name;
    }
    public function getName(): ?string
    {
        return $this->attributes["name"];
    }

    public function setDescription(string $description): void
    {
        $code = trim(strip_tags($description));

        if (strlen($description) < 20) {
            throw new \InvalidArgumentException("A Descrição da Categoria deve ter pelo menos 20 caracteres.");
        }

        $this->attributes["description"] = $description;
    }

    public function getDescription(): ?string
    {
        return $this->attributes["description"];
    }

    public function getCategoryByName(string $name): ?self
    {
        return $this->where("name", "=", $name)->first();
    }
}