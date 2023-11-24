<?php

require_once "src/repos/interfaces/IUserRepo.php";
require_once "src/repos/Repo.php";
require_once "src/models/LinkyUser.php";
require_once "src/enums/UserRole.php";

class UserRepo extends Repo implements IUserRepo
{
    public function all(): array
    {
        $users = array();

        $results = $this->db
            ->connect()
            ->query('SELECT * FROM LinkyUser')
            ->fetchAll();

        foreach ($results as $result) {
            $users[] = $this->mapToObject($result);
        }
        return $users;
    }

    public function findById(string $userId): ?LinkyUser
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkyUser WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }
        return $this->mapToObject($result);
    }

    public function findByUserName(string $user_name): ?LinkyUser
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkyUser WHERE user_name = :user_name');
        $stmt->execute(['user_name' => $user_name]);
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

    public function insert(LinkyUser $user): LinkyUser
    {
        $sql = <<<SQL
        INSERT INTO LinkyUser (user_id, user_name, email, password_hash, email_confirmed, role, refresh_token, refresh_token_exp)
        VALUES (:user_id, :user_name, :email, :password_hash, :email_confirmed, :role, :refresh_token, :refresh_token_exp);
    SQL;

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'user_id' => $user->user_id,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'password_hash' => $user->password_hash,
            'email_confirmed' => (int)$user->email_confirmed,
            'role' => $user->role->name, // Assuming UserRole is an enum
            'refresh_token' => $user->refresh_token,
            'refresh_token_exp' => $user->refresh_token_exp?->format('Y-m-d H:i:s'),
        ]);

        return $this->findById($user->user_id);
    }

    public function update(LinkyUser $user): LinkyUser
    {
        $sql = <<<SQL
        UPDATE LinkyUser
        SET
            user_name = :user_name,
            email = :email,
            password_hash = :password_hash,
            email_confirmed = :email_confirmed,
            role = :role,
            refresh_token = :refresh_token,
            refresh_token_exp = :refresh_token_exp
        WHERE user_id = :user_id;
    SQL;

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'user_id' => $user->user_id,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'password_hash' => $user->password_hash,
            'email_confirmed' => (int)$user->email_confirmed,
            'role' => $user->role->name, // Assuming UserRole is an enum
            'refresh_token' => $user->refresh_token,
            'refresh_token_exp' => $user->refresh_token_exp?->format('Y-m-d H:i:s'),
        ]);

        return $this->findById($user->user_id);
    }

    public function delete(string $userId): bool
    {
        $stmt = $this->db->connect()->prepare('DELETE FROM LinkyUser WHERE user_id = :user_id');
        return $stmt->execute(['user_id' => $userId]);
    }

    private function mapToObject(array $userData): LinkyUser
    {
        return new LinkyUser(
            user_name: $userData['user_name'],
            email: $userData['email'],
            password_hash: $userData['password_hash'],
            user_id: $userData['user_id'],
            email_confirmed: (bool)$userData['email_confirmed'],
            role: UserRole::from($userData['role']),
            refresh_token: $userData['refresh_token'],
            refresh_token_exp: $userData['refresh_token_exp'] ? new DateTime($userData['refresh_token_exp']) : null
        );
    }
}
