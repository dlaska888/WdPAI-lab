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
     * @param int $userId
     * @return ?LinkyUser
     */
    public function find(int $userId): ?LinkyUser;

    /**
     * Save a user.
     *
     * This creates the user if it doesn't exist or updates
     * it if it does.
     *
     * @param LinkyUser $user
     * @return LinkyUser
     */
    public function save(LinkyUser $user): LinkyUser;

    /**
     * Delete a specific user by ID.
     *
     * @param int $userId
     * @return bool
     */
    public function delete(int $userId): bool;

    /**
     * Return the next ID available to uniquely identify a user.
     *
     * @return int
     */
    public function nextIdentity(): int;
}

