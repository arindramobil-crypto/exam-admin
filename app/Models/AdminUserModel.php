<?php
namespace App\Models;
use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table      = 'admin_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama','username','password','role','is_active'];
    protected $useTimestamps = true;
}