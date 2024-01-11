<?php

namespace src\Repos;

use InvalidArgumentException;
use PDO;
use ReflectionClass;
use ReflectionException;
use src\exceptions\NotFoundException;
use src\Helpers\StringHelper;
use src\hydrators\EntityHydrator;
use src\models\Database;
use src\Models\Entities\Entity;
use src\Repos\Interfaces\IRepo;

/**
 * @template T of Entity
 */
abstract class BaseRepo implements IRepo
{
    protected Database $db;

    protected EntityHydrator $hydrator;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->hydrator = new EntityHydrator();
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getEntityName(): string;

    /**
     * @throws ReflectionException
     */
    protected function getTableName(): string
    {
        return StringHelper::pascalToSnake((new ReflectionClass($this->getEntityName()))->getShortName());
    }

    /**
     * @param array<string, mixed> $data
     * @return T
     */
    protected function mapToObject(array $data)
    {
        $className = $this->getEntityName();
        return $this->hydrator->hydrate($data, new $className);
    }

    /**
     * @param Entity $model
     * @return array
     */
    protected function mapToArray(Entity $model): array
    {
        $className = $this->getEntityName();

        if (!$model instanceof $className) {
            throw new InvalidArgumentException("Invalid Entity type provided for {$this->getEntityName()}");
        }

        return $this->hydrator->extract($model);
    }

    /**
     * @return list<T>
     * @throws ReflectionException
     */
    public function findAll(): array
    {
        $models = array();

        $results = $this->db
            ->connect()
            ->query("SELECT * FROM {$this->getTableName()} ORDER BY date_created")
            ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $models[] = $this->mapToObject($result);
        }

        return $models;
    }

    /**
     * @param string $id
     * @return T
     * @throws ReflectionException
     * @throws NotFoundException
     */
    public function findById(string $id)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE id = :id";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$result){
            throw new NotFoundException("{${$this->getEntityName()}} with this id not found");
        }

        return $this->mapToObject($result);
    }

    /**
     * @param T $model
     * @return T
     * @throws ReflectionException|NotFoundException
     */
    public function insert($model)
    {
        $data = $this->mapToArray($model);
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->getTableName()} ($columns) VALUES ($values)";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($data);

        return $this->findById($model->id);
    }

    /**
     * @param T $model
     * @return T
     * @throws ReflectionException|NotFoundException
     */
    public function update($model)
    {
        $data = $this->mapToArray($model);
        $updates = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));

        $sql = "UPDATE {$this->getTableName()} SET $updates WHERE id = :id";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute($data);

        return $this->findById($model->id);
    }

    /**
     * @param string $id
     * @return bool
     * @throws ReflectionException
     */
    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->getTableName()} WHERE id = :id";

        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
