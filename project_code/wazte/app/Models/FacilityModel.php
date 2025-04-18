<?php
namespace App\Models;

use CodeIgniter\Model;

class FacilityModel extends Model
{
    protected $table = 'facilities';
    protected $primaryKey = 'facility_ID';    // adjust if yours is named differently
    protected $returnType = 'array';
    protected $allowedFields = [
        'locationID',
        'dateadded',
        'AddedBy',
        'contactNo',
        'name',
        'Description',
        'BusinessHours',
    ];
    // no automatic timestamps here
}
