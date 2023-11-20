<?php

require_once "src/repos/interfaces/IUserRepo.php";
require_once "src/models/LinkyUser.php";
require_once "src/enums/UserRole.php";

class UserRepo implements IUserRepo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $users = array();

        $results = $this->db
            ->query('SELECT * FROM LinkyUser')
            ->fetchAll();

        foreach ($results as $result) {
            $users[] = $this->mapToObject($result);
        }
        return $users;
    }

    public function find(int $userId): ?LinkyUser
    {
        $stmt = $this->db->prepare('SELECT * FROM LinkyUser WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        if (!$result) {
            return null;
        }
        return $this->mapToObject($result);
    }

    public function save(LinkyUser $user): LinkyUser
    {
        $sql = <<<SQL
            INSERT INTO LinkyUser (user_id, user_name, email, password_hash, email_confirmed, role, refresh_token, refresh_token_exp)
            VALUES (:user_id, :user_name, :email, :password_hash, :email_confirmed, :role, :refresh_token, :refresh_token_exp)
            ON CONFLICT (user_id)
            DO
            UPDATE SET
                user_name = :user_name,
                email = :email,
                password_hash = :password_hash,
                email_confirmed = :email_confirmed,
                role = :role,
                refresh_token = :refresh_token,
                refresh_token_exp = :refresh_token_exp;
        SQL;

        $stmt = $this->db->prepare($sql);
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

        return $this->find($user->user_id);
    }

    public function delete(int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM LinkyUser WHERE user_id = :user_id');
        return $stmt->execute(['user_id' => $userId]);
    }

    public function nextIdentity(): int
    {
        $stmt = $this->db->prepare("SELECT nextval('users_user_id_seq')");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function mapToObject(array $userData): LinkyUser
    {
        $user = new LinkyUser();

        $user->user_id = $userData['user_id'];
        $user->user_name = $userData['user_name'];
        $user->email = $userData['email'];
        $user->password_hash = $userData['password_hash'];
        $user->email_confirmed = (bool)$userData['email_confirmed'];
        $user->role = UserRole::from($userData['role']);
        $user->refresh_token = $userData['refresh_token'];
        $user->refresh_token_exp = $userData['refresh_token_exp'] ? new DateTime($userData['refresh_token_exp']) : null;

        return $user;
    }

}
