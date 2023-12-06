<?php

namespace src\Repos\Interfaces;

interface IRepo
{
public function all(): array;

public function findById(string $id): ?object;

public function insert(object $entity): object;

public function update(object $entity): object;

public function delete(string $id): bool;
}
