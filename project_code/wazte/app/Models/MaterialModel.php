<?php
namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $allowedFields = ['Material', 'icon'];

    // Returns materials with an alias for the name column
    public function getMaterials()
    {
        return $this->select('tags_id as id,Material as name, icon')->findAll();
    }
}
