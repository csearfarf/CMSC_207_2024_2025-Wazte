<?php
namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'tags_ID';
    protected $returnType = 'array';
    protected $allowedFields = [
        'Material',
        'icon',
    ];
}
