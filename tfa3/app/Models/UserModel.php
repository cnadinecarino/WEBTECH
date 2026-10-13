<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    public const USERNAME_MAX_LENGTH = 60;
    public const FULL_NAME_MAX_LENGTH = 150;
    public const ROLE_MAX_LENGTH = 60;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['username', 'full_name', 'role', 'avatar'];

    /**
     * Let the database collation decide equality, just like the unique index.
     * Excluding the selected account permits an unchanged username on edits.
     */
    public function usernameExists(string $username, ?int $ignoreId = null): bool
    {
        $builder = $this->db->table($this->table)->where('username', $username);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }
}
