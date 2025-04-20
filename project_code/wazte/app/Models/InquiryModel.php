<?php
namespace App\Models;

use CodeIgniter\Model;

class InquiryModel extends Model
{
    protected $table = 'inquiries';
    protected $primaryKey = 'inquiry_ID';
    protected $allowedFields = ['name', 'email', 'facility_ID'];
    protected $useTimestamps = false;
}
