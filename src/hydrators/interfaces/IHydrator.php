<?php

namespace src\hydrators\interfaces;

use src\Models\Entities\Entity;

interface IHydrator
{
    public function hydrate(array $data, Entity $model): Entity;
    public function extract(Entity $model): array;
}