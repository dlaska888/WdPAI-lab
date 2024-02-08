<?php

namespace src\Repos;

use PDO;
use src\Models\Entities\LinkyUser;

class UserRepo extends BaseRepo
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getEntityName(): string
    {
        return LinkyUser::class;
    }

    public function findByUserName(string $userName): ?LinkyUser
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM linky_user WHERE user_name = :user_name');
        $stmt->execute(['user_name' => $userName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$result){
            return null;
        }

        return $this->mapToObject($result);
    }

    public function findByEmail(string $email): ?LinkyUser
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM linky_user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$result){
            return null;
        }

        return $this->mapToObject($result);
    }

}
