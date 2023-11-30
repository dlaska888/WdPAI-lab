<?php

require_once "src/Database.php";
require_once "src/repos/interfaces/IRepo.php";

abstract class BaseRepo implements IRepo
{
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    protected abstract function getTableName(): string;

    protected abstract function mapToObject(array $data): object;

    protected abstract function mapToArray(object $entity): array;

    protected abstract function getIdName(): string;

    public function all(): array
    {
        $entities = array();

        $results = $this->db
            ->connect()
            ->query("SELECT * FROM {$this->getTableName()}")
            ->fetchAll();

        foreach ($results as $result) {
            $entities[] = $this->mapToObject($result);
        }

        return $entities;
    }

    public function findById(string $id): ?object
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->getTableName()} WHERE {$this->getIdName()} = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return $this->mapToObject($result);
    }

    public function insert(object $entity): object
    {
        $columns = implode(', ', array_keys($this->mapToArray($entity)));
        $values = ':' . implode(', :', array_keys($this->mapToArray($entity)));

        $sql = "INSERT INTO {$this->getTableName()} ($columns) VALUES ($values)";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($this->mapToArray($entity));

        return $this->findById($entity->{$this->getIdName()});
    }

    public function update(object $entity): object
    {
        $updates = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($this->mapToArray($entity))));

        $sql = "UPDATE {$this->getTableName()} SET $updates WHERE {$this->getIdName()} = :{$this->getIdName()}";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($this->mapToArray($entity));

        return $this->findById($entity->{$this->getIdName()});
    }

    public function delete(string $id): bool
    {
        $stmt = $this->db->connect()->prepare("DELETE FROM {$this->getTableName()} WHERE {$this->getIdName()} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
