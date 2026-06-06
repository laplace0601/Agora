<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    // Definimos qué campos se pueden insertar/actualizar
    protected $allowedFields = ['email', 'password'];
}
