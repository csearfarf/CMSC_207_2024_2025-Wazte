<?php
namespace App\Models;

use CodeIgniter\Model;

class FacilityTagsModel extends Model
{
    protected $table = 'facilitytags';
    protected $primaryKey = 'facilitytags_ID';
    protected $returnType = 'array';
    protected $allowedFields = [
        'facility_ID',
        'tags_ID',
    ];
}
