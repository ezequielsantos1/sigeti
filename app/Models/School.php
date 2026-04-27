<?php

namespace App\Models;

use App\Core\AbstractModel;
use http\Exception\InvalidArgumentException;

class School extends AbstractModel
{
    protected string $table = 'schools';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        "name",
        "code",
        "address"
    ];

    protected array $required = [
        "name" => "O NOME é obrigatório.",
        "code" => "O CÒDIGO é obrigatório.",
        "address" => "O campo ENDEREÇO é obrigatório."
    ];

    protected bool $timestamps = true;

    public function getId(): ?int
    {
        return $this->attributes["id"];
    }

    public function setName(string $name): void
    {
        $name = trim(strip_tags($name));

        if (strlen($name) < 15) {
            throw new \InvalidArgumentException("O nome da escola deve ter pelo menos 15 caracteries.");
        }

        $this->attributes["name"] = $name;
    }

    public function getName(): ?string
    {
        return $this->attributes["name"];
    }

    public function setCode(string $code): void
    {
        $code = trim($code);
        if (strlen($code) !== 8) {
            throw new \InvalidArgumentException("O código da escola deve ter exatamente 8 caracteres.");
        }

        $this->attributes["code"] = $code;
    }

    public function getCode(): ?string
    {
        return $this->attributes["code"];
    }

    public function setAddress(string $address): void
    {
        $address = trim(strip_tags($address));

        if (strlen($address) < 20) {
            throw new \InvalidArgumentException("O endereço deve ter pelo menos 20 caracteres.");
        }

        $this->attributes["address"] = $address;
    }

    public function getAddress()
    {
        return $this->attributes["address"];
    }

    public function findBySchool(?string $school): ?School
    {
        return (new static())->where("school_id", "=", $school);
    }

    public function findByCode(string $code): ?self
    {
        return (new static())->where("code", "=", $code)->first();
    }

    public function getSchoolByName(string $name): ?self
    {
        return $this->where("name", "=", $name)->first();
    }

    public function existsByName(string $name, ?int $ignoreId = null): bool
    {
        $query = $this->where("name", "=", $name);

        if ($ignoreId) {
            $query->where("id", "!=", $ignoreId);
        }
        return $query->first() !== null;
    }

    public function existsByCode(string $code, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code";
        $params = ['code' => $code];

        if ($ignoreId) {
            $sql .= " AND id != :ignore_id";
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return (int)$statement->fetchColumn() > 0;
    }

    public function validateBusinessRule(?int $ignoreId = null): array
    {
        $errors = [];

        if ($this->existsByName($this->getName(), $ignoreId)) {
            $errors[] = "Já existe escola com esse nome.";
        }

        if ($this->existsByCode($this->getCode(), $ignoreId)) {
            $errors[] = "Já existe escola com esse nome.";
        }
    }
}