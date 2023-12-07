<?php

namespace src\Repos;

use DateTime;
use src\Enums\UserRole;
use src\Models\Entities\LinkyUser;

class UserRepo extends BaseRepo
{
    protected function getTableName(): string
    {
        return 'LinkyUser';
    }

    protected function getIdName(): string
    {
        return 'user_id';
    }

    protected function mapToObject(array $data): LinkyUser
    {
        return new LinkyUser(
            user_name: $data['user_name'],
            email: $data['email'],
            password_hash: $data['password_hash'],
            user_id: $data['user_id'],
            email_confirmed: (bool)$data['email_confirmed'],
            role: UserRole::from($data['role']),
            refresh_token: $data['refresh_token'],
            refresh_token_exp: $data['refresh_token_exp'] ? new DateTime($data['refresh_token_exp']) : null
        );
    }

    protected function mapToArray(object $entity): array
    {
        return [
            'user_id' => $entity->user_id,
            'user_name' => $entity->user_name,
            'email' => $entity->email,
            'password_hash' => $entity->password_hash,
            'email_confirmed' => (int)$entity->email_confirmed,
            'role' => $entity->role->name,
            'refresh_token' => $entity->refresh_token,
            'refresh_token_exp' => $entity->refresh_token_exp ? $entity->refresh_token_exp->format('Y-m-d H:i:s') : null,
        ];
    }

    public function findByUserName(string $userName): ?LinkyUser
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkyUser WHERE user_name = :user_name');
        $stmt->execute(['user_name' => $userName]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return $this->mapToObject($result);
    }

    public function findByEmail(string $email): ?LinkyUser
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkyUser WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return $this->mapToObject($result);
    }

}
