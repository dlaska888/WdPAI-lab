<?php

namespace LinkyApp\Repos\Interfaces;

use LinkyApp\Models\Entities\Entity;

interface IRepo
{
public function findAll(): array;

public function findById(string $id);

public function insert(Entity $model);

public function update(Entity $model);

public function delete(string $id): bool;
}
