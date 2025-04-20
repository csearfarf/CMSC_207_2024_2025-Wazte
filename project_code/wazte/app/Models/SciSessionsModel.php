<?php
namespace App\Models;

use CodeIgniter\Model;

class SciSessionsModel extends Model
{
    protected $table = 'ci_sessions';     // your sessions table
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['id', 'ip_address', 'timestamp', 'data'];
}
