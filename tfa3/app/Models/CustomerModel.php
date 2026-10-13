<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    public const FULL_NAME_MAX_LENGTH = 150;
    public const EMAIL_MAX_LENGTH = 254;
    public const PHONE_MAX_LENGTH = 40;

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['full_name', 'email', 'phone'];
}
