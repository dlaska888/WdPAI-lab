<?php

namespace src\hydrators\interfaces;

/**
 * Interface IStrategy
 *
 * This interface defines methods for hydrating and extracting data between a database and a model.
 */
interface IStrategy
{
    /**
     * Maps a value from the database to the model.
     *
     * @param mixed $value The value retrieved from the database.
     *
     * @return mixed The hydrated value for the model.
     */
    public function hydrate(mixed $value): mixed;

    /**
     * Maps a value from the model to a format suitable for the database.
     *
     * @param mixed $value The value from the model.
     *
     * @return mixed The extracted value suitable for the database.
     */
    public function extract(mixed $value): mixed;
}
