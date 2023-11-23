<?php

require_once "src/models/LinkyUser.php";

interface IUserRepo
{
    /**
     * Fetch all users.
     *
     * @return LinkyUser[]
     */
    public function all(): array;

    /**
     * Find a specific user by its ID.
     *
     * @param string $userId
     * @return ?LinkyUser
     */
    public function find(string $userId): ?LinkyUser;

    /**
     * Inserts a user.
     *
     * @param LinkyUser $user
     * @return LinkyUser
     */
    public function insert(LinkyUser $user): LinkyUser;

    /**
     * Updates a user.
     *
     * @param LinkyUser $user
     * @return LinkyUser
     */
    public function update(LinkyUser $user): LinkyUser;

    /**
     * Delete a specific user by ID.
     *
     * @param int $userId
     * @return bool
     */
    public function delete(string $userId): bool;

}

