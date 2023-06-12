<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'name', 'password', 'image', 'is_active'];
    protected $useTimestamps = true;

    public function getUser($option, $value)
    {
        $user = $this->where([$option => $value])->get()->getResultArray();
        if ($user) {
            $user = $user[0];
        } else {
            $user = null;
        }
        return $user;
    }
}
