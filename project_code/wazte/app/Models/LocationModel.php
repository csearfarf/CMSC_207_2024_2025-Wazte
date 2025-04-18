<?php
namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'locations';      // your locations table
    protected $primaryKey = 'location_ID';
    protected $returnType = 'array';
    protected $allowedFields = [
        'lat',
        'lng',
        'address',
    ];
    // no automatic timestamps here
}
