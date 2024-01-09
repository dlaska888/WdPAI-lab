<?php

namespace src\Repos;

use src\exceptions\BadRequestException;
use src\exceptions\NotFoundException;
use PDOException;
use src\models\Database;
use src\Repos\Interfaces\IRepo;

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
            ->query("SELECT * FROM {$this->getTableName()} ORDER BY date_created")
            ->fetchAll();

        foreach ($results as $result) {
            $entities[] = $this->mapToObject($result);
        }

        return $entities;
    }

    public function findById(string $id): object
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE {$this->getIdName()} = :id";

        try {
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();
        } catch (PDOException $e) {
            throw new NotFoundException($e->getMessage());
        }

        if (!$result) {
            throw new NotFoundException("{$this->getTableName()} with such id not found");
        }

        return $this->mapToObject($result);
    }

    public function insert(object $entity): object
    {
        $columns = implode(', ', array_keys($this->mapToArray($entity)));
        $values = ':' . implode(', :', array_keys($this->mapToArray($entity)));

        $sql = "INSERT INTO {$this->getTableName()} ($columns) VALUES ($values)";

        try {
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->execute($this->mapToArray($entity));
        } catch (PDOException $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $this->findById($entity->{$this->getIdName()});
    }

    public function update(object $entity): object
    {
        $updates = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($this->mapToArray($entity))));

        $sql = "UPDATE {$this->getTableName()} SET $updates WHERE {$this->getIdName()} = :{$this->getIdName()}";


        try {
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->execute($this->mapToArray($entity));
        } catch (PDOException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $this->findById($entity->{$this->getIdName()});
    }

    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->getTableName()} WHERE {$this->getIdName()} = :id";


        try {
            $stmt = $this->db->connect()->prepare($sql);
            $result = $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $result;
    }
}
